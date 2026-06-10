const fs = require('fs');
const path = require('path');

module.exports = {
  allowCypressEnv: false,

  e2e: {
    setupNodeEvents(on, config) {
      on('task', {
        resetData() {
          const seedDir = path.resolve(__dirname, 'cypress/seed');
          const dataDir = path.resolve(__dirname, 'data');
          ['products.json', 'orders.json', 'users.json'].forEach(f => {
            fs.copyFileSync(path.join(seedDir, f), path.join(dataDir, f));
          });
          console.log('[Cypress] Seed restored from cypress/seed/');
          return null;
        }
      });
    },
  },
};
