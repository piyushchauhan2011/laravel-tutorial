import { expect, test } from '@playwright/test';

test('user can create an issue from the web form', async ({ page }) => {
  await page.goto('/issues');

  await expect(page.getByRole('heading', { name: 'Issue Tracker' })).toBeVisible();

  await page.getByRole('link', { name: 'Report new issue' }).click();

  await page.getByLabel('Title').fill('Playwright discovered checkout error');
  await page.getByLabel('Description').fill(
    'The browser automation run found a reproducible checkout exception that needs triage immediately.',
  );
  await page.getByLabel('Priority').selectOption('high');
  await page.getByLabel('Reported by').fill('playwright-bot');

  await page.getByRole('button', { name: 'Submit Issue' }).click();

  await expect(page.getByText('Issue reported and queued for severity assessment.')).toBeVisible();
  await expect(page.getByText('Playwright discovered checkout error')).toBeVisible();
});
