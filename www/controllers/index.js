'use strict';

module.exports = {
   paths: '/',

   get: async function(req, res) {
      const app = req.app.app;
      const date = await app.mysql.query('select max(logoReleased) maxDate from al_alliances');
      const maxDate = date[0].maxDate;
      const latest = await app.mysql.query('select allianceID, shortName, allianceName from al_alliances where logoReleased = ? order by allianceCreation', [maxDate]);
      const alliances = await app.mysql.query('select allianceID, shortName, allianceName, if (allianceCreation is null, null, concat(year(allianceCreation), " ", monthname(allianceCreation))) created from al_alliances where logoReleased is not null order by allianceCreation desc');

      const grouped = {};
      for (const info of alliances) {
        if (grouped[info.created] == undefined) grouped[info.created] = [];
        grouped[info.created].push(info);
      }

      return {
          package: {
            maxDate: maxDate,
            latest: latest, 
            grouped: grouped
         },
         ttl: 5,
         view: 'index.pug'
      };
   }
}