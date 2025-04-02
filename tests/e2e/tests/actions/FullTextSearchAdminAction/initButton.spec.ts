import {expect, Page, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {createPageWithContent} from "../../../../../../../tests/e2e/helpers/page";
import {resetIndex} from "../../../helpers/db";
import {errorShouldBe} from "../../../../../../../tests/e2e/helpers/alert";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login} from "../../../../../../../tests/e2e/helpers/login";
import {engineProvider} from "../../../provider/engineProvider";
import {udpateEngineConfig} from "../../../helpers/config";

const getInitButton = async (page: Page) => {
    return page.locator('#fullTextSearch-engineConfigure button');
}

engineProvider.forEach(engine => {
    test.describe(`${engine.driver} - Have a full feature init button`, () => {
        let page: Page;
        test.beforeAll(async ({ browser }) => {
            page = await browser.newPage(); // Optimisation to chain all tests

            resetEnv();
            await udpateEngineConfig(page, engine);
            await resetIndex(page);
            await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
        });

        test(`${engine.driver} - Init button is unititialized by default`, async () => {
            await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
            const initButton = await getInitButton(page);
            await expect(initButton).toBeVisible();
            await expect(initButton).toContainText('Initialiser');
        });

        test(`${engine.driver} - Can init engine`, async () => {
            const initButton = await getInitButton(page);
            await initButton.click();
            await expect(initButton).toBeDisabled();
            await expect(initButton).toContainText('Ré indexer', {timeout: 10000});
            await errorShouldBe(page, 'Initialisation complétée');
        });

        test(`${engine.driver} - Can force reindex`, async () => {
            const initButton = await getInitButton(page);
            await initButton.click();
            await expect(initButton).toBeDisabled();
            await expect(initButton).toContainText('Ré indexer', {timeout: 10000});
            await errorShouldBe(page, 'Initialisation complétée');
        });
    });
})


test(`Print error message on init exception`, async ({page}) => {
    resetEnv();
    await resetIndex(page);
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
    const initButton = await getInitButton(page);

    await page.route('/?api/fulltextsearch/admin/init', async route => {
        await route.fulfill({
            status: 500,
            json: {
                exceptionMessage: 'Exception message',
            }});
    });
    await initButton.click();
    await errorShouldBe(page, 'Exception message');
});

test(`Print error message on init unknown error`, async ({page}) => {
    resetEnv();
    await resetIndex(page);
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
    const initButton = await getInitButton(page);

    await page.route('/?api/fulltextsearch/admin/init', async route => {
        await route.fulfill({
                status: 500,
            }
        )});
    await initButton.click();
    await errorShouldBe(page, 'Une erreur interne est survenue.');
});
