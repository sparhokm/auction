import { launch } from 'puppeteer'
import { Before, After, Status, setDefaultTimeout } from '@cucumber/cucumber'
import { CustomWorld } from './world'

setDefaultTimeout(10 * 1000)

Before({ timeout: 30000 }, async function (this: CustomWorld) {
  this.browser = await launch({
    args: [
      '--disable-dev-shm-usage',
      '--no-sandbox'
    ]
  })
  this.page = await this.browser.newPage()
  await this.page.setViewport({ width: 1280, height: 720 })
})

After(async function (this: CustomWorld, testCase) {
  if (this.page) {
    if (testCase.result && testCase.result.status === Status.FAILED) {
      const screenShot = await this.page.screenshot({ encoding: 'base64', fullPage: true })
      this.attach(screenShot, 'image/png')
      const name = testCase.pickle.uri.replace(/^features\//, '') +
          '-' +
          testCase.pickle.name.toLowerCase().replace(/[^\w]/g, '_') +
          '.png'
      await this.page.screenshot({ path: 'var/' + name, fullPage: true })
    }
    await this.page.close()
  }
  if (this.browser) {
    await this.browser.close()
  }
})
