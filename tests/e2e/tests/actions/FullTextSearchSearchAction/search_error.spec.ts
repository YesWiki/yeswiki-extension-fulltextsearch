import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {initEngine, resetIndex} from "../../../helpers/db";
import {createPageWithContent, removePage} from "../../../../../../../tests/e2e/helpers/page";
import {replaceEditorTextCallback, replaceEditorTextNewContent} from "../../../../../../../tests/e2e/helpers/editor";
import {errorShouldBe} from "../../../../../../../tests/e2e/helpers/alert";

test.beforeEach(async ({page}) => {
    resetEnv();
    await resetIndex(page);
    await initEngine(page);
    await createPageWithContent(page, 'fullTextSearchSearch', `{{ FullTextSearchSearch }}`)
})

test(`Print generic error message in search error`, async ({ page }) => {

    await page.route('/?api/fulltextsearch/search', async route => {
        await route.fulfill({
                status: 500,
            }
        )});

    await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
    await errorShouldBe(page, 'Une erreur interne est survenue.');
});

test(`Print error message from server in search error`, async ({ page }) => {

    await page.route('/?api/fulltextsearch/search', async route => {
        await route.fulfill({
                status: 500,
                body: JSON.stringify({exceptionMessage: 'Custom error message'})
            }
        )});

    await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
    await errorShouldBe(page, 'Custom error message');
});

test(`Print timeout message`, async ({ page }) => {

    await page.route('/?api/fulltextsearch/search', async route => {
        await new Promise(f => setTimeout(f, 5100));
        await route.fulfill({
                status: 500,
            }
        )});

    await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
    await expect(page.locator('.toast-message')).toBeVisible({timeout: 6000}); // Wait for timeout toast
    await errorShouldBe(page, 'Erreur de recherche. Cela provient probablement d\'une surcharge du serveur : essayez de diminuer le nombre de termes, d\'augmenter les resources du serveur ou de changer le moteur d\'indexation.');
});