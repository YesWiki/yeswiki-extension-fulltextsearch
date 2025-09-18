import * as fs from "node:fs";
import {expect, Page} from "@playwright/test";
import {ADMIN_PASSWORD, ADMIN_USERNAME, login, logout} from "../../../../../tests/e2e/helpers/login";
import {createPageWithContent} from "../../../../../tests/e2e/helpers/page";
import {Engine} from "../provider/engineProvider";

const updateConfigRequest = async (page: Page, config: object) => {
    await login(page, ADMIN_USERNAME, ADMIN_PASSWORD);
    const res = await page.request.post('/?api/ci/update_config', {
        data: config
    });

    expect(res.ok()).toBeTruthy();
    await logout(page);
}

export const udpateEngineConfig = async (page: Page, engine: Engine) => {
    await updateConfigRequest(page,
        {
            fulltextsearch: {
                engine_config: engine
            }
        }
    );
}

export const udpateRenderingLengthCropConfig = async (page: Page, engine: Engine, lengthCrop: number) => {
    await updateConfigRequest(page,
        {
            fulltextsearch: {
                engine_config: engine,
                rendering: {
                    length_crop: lengthCrop
                }
            }
        }
    );
}

export const udpateRenderingLengthExcerptMax = async (page: Page, engine: Engine, lengthExcerpt: number) => {
    await updateConfigRequest(page,
        {
            fulltextsearch: {
                engine_config: engine,
                rendering: {
                    length_excerpt_max: lengthExcerpt
                }
            }
        }
    );
}
