import {expect, Page, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {resetIndex} from "../../../helpers/db";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login} from "../../../../../../../tests/e2e/helpers/login";
import {createPageWithContent} from "../../../../../../../tests/e2e/helpers/page";
import {errorShouldBe} from "../../../../../../../tests/e2e/helpers/alert";
import {udpateEngineConfig} from "../../../helpers/config";


test(`Print error message on config unknown driver`, async ({page}) => {
    resetEnv();
    await udpateEngineConfig(page, {
        driver: 'unknow',
    })
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`);
    await expect(page.locator('.alert-danger')).toContainText('Le driver de recherche est introuvable.');
});

test(`Print error message on config typescript connexion error`, async ({page}) => {
    resetEnv();
    await udpateEngineConfig(page, {
        driver: 'typesense',
        typesense_config: {
            api_key: 'unknow',
            host: 'unknow',
            port: 1234,
            protocol: 'http',
        }
    })
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`);
    await expect(page.locator('.alert-danger')).toContainText('Could not resolve host: unknow for "http://unknow:1234/collections/pages".');
});

test(`Print error message on config typescript api key error`, async ({page}) => {
    resetEnv();
    await udpateEngineConfig(page, {
        driver: 'typesense',
        typesense_config: {
            api_key: 'unknow',
            host: 'typesense',
            port: 8108,
            protocol: 'http',
        }
    })
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`);
    await expect(page.locator('.alert-danger')).toContainText('Forbidden - a valid `x-typesense-api-key` header must be sent');
});
