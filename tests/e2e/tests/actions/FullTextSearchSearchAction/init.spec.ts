import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {initEngine, resetIndex} from "../../../helpers/db";
import {createPageWithContent} from "../../../../../../../tests/e2e/helpers/page";
import {engineProvider} from "../../../provider/engineProvider";
import {udpateEngineConfig} from "../../../helpers/config";

engineProvider.forEach(engine => {
    test.beforeEach(async ({page}) => {
        resetEnv();
        await udpateEngineConfig(page, engine);
        await resetIndex(page);
    })

    test(`${engine.driver} - Print an error message if engine not configured`, async ({ page }) => {
        await createPageWithContent(page, 'fullTextSearchSearch', `{{ FullTextSearchSearch }}`)
        await expect(page.locator('.alert-danger')).toHaveText('Erreur : merci de configurer le moteur d\'indexation avant d\'utiliser la recherche');
        await expect(page.locator('[name="fullTextSearch_search"]')).toHaveCount(0);
    });
});
