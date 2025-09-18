import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {initEngine, resetIndex} from "../../../helpers/db";
import {createPageWithContent, removePage} from "../../../../../../../tests/e2e/helpers/page";
import {replaceEditorTextCallback, replaceEditorTextNewContent} from "../../../../../../../tests/e2e/helpers/editor";
import {setPagePermission} from "../../../../../../../tests/e2e/helpers/permissions";
import {logout} from "../../../../../../../tests/e2e/helpers/login";
import {engineProvider} from "../../../provider/engineProvider";
import {
    udpateEngineConfig,
    udpateRenderingLengthCropConfig,
    udpateRenderingLengthExcerptMax
} from "../../../helpers/config";

engineProvider.forEach(engine => {
    test.describe('engine ' + engine.driver, () => {
        test.beforeEach(async ({page}) => {
            resetEnv();
            await udpateEngineConfig(page, engine);
            await resetIndex(page);
            await initEngine(page);
            await createPageWithContent(page, 'fullTextSearchSearch', `{{ FullTextSearchSearch }}`)
        })
        test(`${engine.driver} - Search existing`, async ({ page }) => {
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Bac sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
            await expect(firstResult.locator('h4')).toContainText('BacASable');
            await expect(firstResult).toContainText('Bac à sable Premiers défis à réaliser');
        });

        test(`${engine.driver} - Search result should not return page without permission`, async ({ page }) => {
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            await setPagePermission(page, 'BacASable', '@admins', null);
            await logout(page);

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
        });

        test(`${engine.driver} - Search result should not return excluded page`, async ({ page }) => {
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            await page.goto('/?fullTextSearch');
            await page.locator('.dataTables_filter input').fill('sable')
            await page.getByRole('button', { name: 'Indexé' }).click();

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
        });

        test(`${engine.driver} - Search results should be updated with page creation`, async ({ page }) => {
            await createPageWithContent(page, 'LoremIpsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.');
            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Lorem ipsum');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
            await expect(firstResult.locator('h4')).toContainText('LoremIpsum');
            await expect(firstResult).toContainText('Lorem ipsum dolor sit amet, consectetur…Donec a diam lectus. Sed sit amet ipsum mauris.');
        });

        test(`${engine.driver} - Search results should be updated with page change`, async ({ page }) => {
            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Lorem ipsum dolor');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');

            await page.goto('/?BacASable');
            await page.getByRole('link', { name: 'Éditer la page' }).click();
            await replaceEditorTextNewContent(page, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.');
            await page.getByRole('button', { name: 'Sauver' }).first().click();

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Lorem ipsum dolor');

            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');
            const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
            await expect(firstResult.locator('h4')).toContainText('BacASable');
            await expect(firstResult).toContainText('Lorem ipsum dolor sit amet');
        });

        test(`${engine.driver} - Search results should be updated with page remove`, async ({ page }) => {
            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            await removePage(page, "BacASable");

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('sable');

            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
        });

        test(`${engine.driver} - Search results limit should be configurable`, async ({ page }) => {
            await createPageWithContent(page, 'lipsum1', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);
            await createPageWithContent(page, 'lipsum2', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);
            await createPageWithContent(page, 'lipsum3', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);
            await createPageWithContent(page, 'lipsum4', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);
            await createPageWithContent(page, 'lipsum5', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);

            await page.goto('/?fullTextSearchSearch');

            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Lorem ipsum dolor');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 5');

            await page.getByRole('link', { name: 'Éditer la page' }).click();
            await replaceEditorTextNewContent(page, '{{ FullTextSearchSearch limit="3" }}');
            await page.getByRole('button', { name: 'Sauver' }).first().click();
            await page.waitForLoadState();

            await page.locator('[name="fullTextSearch_search"]').pressSequentially('Lorem ipsum dolor');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 3');
        });

        test(`${engine.driver} - Search results should take care of length_crop config`, async ({ page }) => {
            await udpateRenderingLengthCropConfig(page,engine,  10);
            await createPageWithContent(page, 'lipsum1', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('ipsum');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
            await expect(firstResult.locator('h4')).toContainText('lipsum1');
            await expect(firstResult.locator('.fullTextSearch_searchresult_item_excerpt')).toHaveText('[...] m ipsum dolor…amet ipsum m [...]');
        });

        test(`${engine.driver} - Search results should take care of length_result_max config`, async ({ page }) => {
            await udpateRenderingLengthExcerptMax(page, engine, 10);
            await createPageWithContent(page, 'lipsum1', `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris.`);

            await page.goto('/?fullTextSearchSearch');
            await page.locator('[name="fullTextSearch_search"]').pressSequentially('ipsum');
            await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

            const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
            await expect(firstResult.locator('h4')).toContainText('lipsum1');
            await expect(firstResult.locator('.fullTextSearch_searchresult_item_excerpt')).toContainText('[...] Lorem ipsum dolor sit amet, consectetur adipiscing [...]');
        });
    });
});
