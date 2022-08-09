'use strict';

async function f(app) {
    await updateAlliances(app);
	await detectLogos(app);
}

module.exports = {
    exec: f,
    span: 86400,
    offset: -43200 // noon
}

async function updateAlliances(app) {
    const res = await app.phin({url: 'https://esi.evetech.net/latest/alliances/', timeout: 5000});
    if (res.statusCode != 200) return;

    var alliances = JSON.parse(res.body);
    console.log('Updating', alliances.length, 'alliances');

    await app.mysql.query('update al_alliances set memberCount = 0 where memberCount = 1');

    for (const id of alliances) await updateAlliance(app, id);
}

async function updateAlliance(app, id, retry = 0) {
    try {
        if (retry >= 3) return;

        const url = 'https://esi.evetech.net/latest/alliances/' + id + '/';
        const res_info = await app.phin({url: url, timeout: 5000});

        switch (res_info.statusCode) {
            case 502:
                console.log('Retrying', id);
                await app.sleep(1000);
                await updateAlliance(app, id, (retry + 1));
                break;
            case 200:
                const info = JSON.parse(res_info.body);
                await app.mysql.query('insert into al_alliances (allianceID, allianceName, allianceCreation, shortName, lastChecked, memberCount) values (?, ?, ?, ?, now(), 1) \
                        on duplicate key update shortName = ?, allianceName = ?, lastChecked = now(), memberCount = 1', [id, info.name, new Date(info.date_founded), info.ticker, info.ticker, info.name]);
                break;
            default:
                console.error(id, 'Unknown status code', res_info.statusCode);
        }
    } catch (e) {
        console.log(e);
        return await updateAlliance(app, id, (retry + 1));
    }
}

async function detectLogos(app) {
    const result = await app.mysql.query('select * from al_alliances where logoReleased is null and memberCount > 0');
    let count = result.length;
    for (const row of result) {
        const id = row.allianceID;
        const name = row.allianceName;
        const url = 'https://images.evetech.net/alliances/' + id + '/logo?size=128';
        const res = await app.phin({
            url: url,
            followRedirects: false,
            timeout: 5000
        });
        const md5 = app.md5(res.body.toString());
        if (res.statusCode == 200 && md5 != '8585a6958049d7b1cad620866e344bbf') {
            await app.mysql.query('update al_alliances set logoReleased = now() where allianceID = ?', [id]);
            console.log('Alliance ', id, 'has a logo now.');
        } else {
            await app.mysql.query('update al_alliances set logoReleased = null where allianceID = ?', [id]);
        }
        
        console.log(res.statusCode, md5, url);
        await app.sleep(1000);
    }
}

