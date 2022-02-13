const reporter = require('cucumber-html-reporter')
const {rmSync} = require('fs')

rmSync('var/screenshots',{ recursive: true, force: true });

reporter.generate({
    theme: 'bootstrap',
    jsonFile: 'var/report.json',
    output: 'var/report.html',
    reportSuiteAsScenarios: true,
    scenarioTimestamp: true,
    launchReport: false,
    storeScreenshots: true,
    noInlineScreenshots: true,
    screenshotsDirectory: 'var/screenshots',
})
