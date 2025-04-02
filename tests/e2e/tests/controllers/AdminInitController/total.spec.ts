import {expect, test} from "@playwright/test";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login} from "../../../../../../../tests/e2e/helpers/login";

const TARGET = '?api/fulltextsearch/admin/total';
test(`Access should no be granted to anonymous`, async ({ page }) => {
   const res = await page.request.get(TARGET);
    expect(res.status()).toBe(401);
});

test(`Access should be granted to admins`, async ({ page }) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    const res = await page.request.get(TARGET);
    expect(res.status()).toBe(200);
});
