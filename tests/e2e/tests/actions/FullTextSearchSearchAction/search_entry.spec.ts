import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {initEngine, resetIndex} from "../../../helpers/db";
import {createPageWithContent, removePage} from "../../../../../../../tests/e2e/helpers/page";
import {checkCheckbox} from "../../../../../../../tests/e2e/helpers/bazar";
import {setPagePermission} from "../../../../../../../tests/e2e/helpers/permissions";
import {logout} from "../../../../../../../tests/e2e/helpers/login";
import {engineProvider} from "../../../provider/engineProvider";
import {udpateEngineConfig} from "../../../helpers/config";

engineProvider.forEach(engine => {
    test.beforeEach(async ({page}) => {
        resetEnv();
        await udpateEngineConfig(page, engine);
        await resetIndex(page);
        await initEngine(page);
        await createPageWithContent(page, 'fullTextSearchSearch', `{{ FullTextSearchSearch }}`)
    })

    test(`${engine.driver} - Search existing`, async ({ page }) => {

        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
        await expect(firstResult.locator('.h1')).toContainText('FramasofT');
        await expect(firstResult).toContainText('Nom de la ressource : Framasoft');
    });

    test(`${engine.driver} - Search result should not return entry without permission`, async ({ page }) => {
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        await setPagePermission(page, 'FramasofT', '@admins', null);
        await logout(page);

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
    });

    test(`${engine.driver} - Search result should not return excluded entry`, async ({ page }) => {
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        await page.goto('/?fullTextSearch');
        await page.locator('.dataTables_filter input').fill('framasoft')
        await page.getByRole('button', { name: 'Indexé' }).click();

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
    });

    test(`${engine.driver} - Search results should be updated with entry creation`, async ({ page }) => {
        await page.goto('/?SaisirRessource');
        await page.locator('[name="bf_titre"]').fill('Lorem ipsum dolor');
        await checkCheckbox(page.locator('.yw-main-content'), 'Site web ressource');
        await page.getByRole('button', { name: 'Valider' }).click();

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('lorem');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
        await expect(firstResult.locator('.h1')).toContainText('LoremIpsumDolor');
    });

    test(`${engine.driver} - Search results should be updated with entry change`, async ({ page }) => {
        await page.goto('/?FramasofT');
        await page.getByRole('link', { name: 'Éditer la page' }).click();
        await page.locator('[name="bf_titre"]').fill('Lorem ipsum dolor');
        await page.getByRole('button', { name: 'Valider' }).click();

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('lorem ipsum dolor');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
        await expect(firstResult.locator('.h1')).toContainText('FramasofT');
        await expect(firstResult).toContainText('Nom de la ressource : Lorem ipsum dolor');
    });

    test(`${engine.driver} - Search results should parse attachments in PDF`, async ({ page }) => {
        await page.goto('/?FramasofT');
        await page.getByRole('link', { name: 'Éditer la page' }).click();
        await page.locator('[name="fichierfichier"]').setInputFiles('/var/www/html/tools/fulltextsearch/tests/e2e/data/lipsum.pdf');
        await page.getByRole('button', { name: 'Valider' }).click();

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('lorem ipsum dolor');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        const firstResult = page.locator('.yw-main-content #fullTextSearch_searchwrapper .fullTextSearch_searchresult_item').first();
        await expect(firstResult.locator('.h1')).toContainText('FramasofT');
        await expect(firstResult).toContainText('Documents : Lorem ipsum dolor');
    });

    test(`${engine.driver} - Search results should be updated with entry remove`, async ({ page }) => {
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');
        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 1');

        await removePage(page, "FramasofT");

        await page.goto('/?fullTextSearchSearch');
        await page.locator('[name="fullTextSearch_search"]').pressSequentially('framasoft');

        await expect(page.locator('.yw-main-content #fullTextSearch_searchwrapper')).toContainText('Nombre de résultats : 0');
    });

});
