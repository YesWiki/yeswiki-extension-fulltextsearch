import {expect, test} from "@playwright/test";
import {resetEnv} from "../../../../../../../tests/e2e/helpers/db";
import {createPageWithContent} from "../../../../../../../tests/e2e/helpers/page";
import {resetIndex} from "../../../helpers/db";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login} from "../../../../../../../tests/e2e/helpers/login";

test.beforeEach(async ({page}) => {
    resetEnv();
    await resetIndex(page);
})

test(`Access should no be granted to anonymous`, async ({ page }) => {
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
    await expect(page.locator('.alert-danger')).toContainText('Action yeswiki\\fulltextsearch\\fulltextsearchadmin : Opération permise aux administrateurs uniquement');
});

test(`Access should be granted to admin`, async ({ page }) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
    await expect(page.locator('.alert-danger')).not.toBeVisible();
});
