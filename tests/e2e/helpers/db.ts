import * as fs from "node:fs";
import {expect, Page} from "@playwright/test";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login, logout} from "../../../../../tests/e2e/helpers/login";
import {createPageWithContent} from "../../../../../tests/e2e/helpers/page";

export const resetIndex = async (page: Page) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await page.request.post('/?api/fulltextsearch/admin/cleanup')
    await logout(page);
}

export const initEngine = async (page: Page) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    await createPageWithContent(page, 'fullTextSearch', `{{ FullTextSearchAdmin }}`)
    const initButton = await page.locator('#fullTextSearch-engineConfigure button');
    await initButton.click();
    await expect(initButton).toContainText('Ré indexer', {timeout: 10000});
}
