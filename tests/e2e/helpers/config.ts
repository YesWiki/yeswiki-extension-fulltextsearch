import * as fs from "node:fs";
import {expect, Page} from "@playwright/test";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login, logout} from "../../../../../tests/e2e/helpers/login";
import {createPageWithContent} from "../../../../../tests/e2e/helpers/page";
import {Engine} from "../provider/engineProvider";

export const udpateEngineConfig = async (page: Page, engine: Engine) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);

    const res = await page.request.post('/?api/ci/update_config', {
        data: {
            fulltextsearch: {
                engine_config: engine
            }
        }
    });
    expect(res.ok()).toBeTruthy();

    await logout(page);
}
