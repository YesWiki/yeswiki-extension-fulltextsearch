import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {initEngine, resetIndex} from "../../../helpers/db";
import {createPageWithContent, removePage} from "../../../../../../../tests/e2e/helpers/page";
import {replaceEditorTextCallback, replaceEditorTextNewContent} from "../../../../../../../tests/e2e/helpers/editor";

test.beforeEach(async ({page}) => {
    resetEnv();
    await resetIndex(page);
    await initEngine(page);
    await createPageWithContent(page, 'fullTextSearchSearch', `{{ FullTextSearchSearch }}`)
})

test(`Print error message in search error`, async ({ page }) => {

    await page.route('/?api/fulltextsearch/search', async route => {
        await route.fulfill({
                status: 500,
            }
        )});

    await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
    await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Une erreur interne est survenue.');
});
