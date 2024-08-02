# Changelog

All notable changes to `laravel-mailcoach` will be documented in this file

## 8.2.0 - 2024-05-31

### What's Changed

* Add support for Resend by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1614

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.1.2...8.2.0

## 8.1.2 - 2024-05-29

- Require new version of spatie/laravel-rate-limited-job-middleware

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.1.1...8.1.2

## 8.1.1 - 2024-05-19

* Fix a small issue with throttle not being reset in time

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.8...8.1.1

## 8.1.0 - 2024-05-15

* No longer throttle creating send models, this will speed up the overall sending of campaigns. Throttling in this step is no longer necessary and was used in the past because sends were also dispatched right away.
* Various improvements to the memory & queries for large campaign sends
* Fix the address normalizer so it works with comma's in a name

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.7...8.0.8

## 8.0.7 - 2024-05-10

### What's Changed

* Replace Markup with Markdown by @sebastiandedeyne in https://github.com/spatie/laravel-mailcoach/pull/1609
* Use strict RFC validation for email addresses
* Refactor simple throttle to work in microseconds for more accurate throttling

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.6...8.0.7

## 8.0.6 - 2024-05-08

* Fix the send test modal nog displaying the from email input when testing an automation mail

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.5...8.0.6

## 8.0.5 - 2024-05-06

* Revert "Feature/unlayer replace entangle (#1598)" - it was causing issues with split tests
* Add email list parameter to search on subscribers

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.4...8.0.5

## 8.0.4 - 2024-05-02

### What's Changed

* Avoid warning on HTML entity decoding by @crishoj in https://github.com/spatie/laravel-mailcoach/pull/1599
* Re-introduce full search on email on subscribers
* Add a way to disable webview through API

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.3...8.0.4

## 8.0.3 - 2024-04-26

### What's Changed

* Feature/unlayer replace entangle by @JordiBontje in https://github.com/spatie/laravel-mailcoach/pull/1598
* Add missing number_format to statistics
* Improve searching on large number of subscribers

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.2...8.0.3

## 8.0.2 - 2024-04-15

* Prevent view exception in conditionAction
* Fix dropdown not being able to scroll in automation actions
* Fix compatibility issues with v7 symfony packages

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.1...8.0.2

## 8.0.1 - 2024-04-12

### What's Changed

* Calling the function openUrlInNewTab after assigning Url as Filament requires it by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1590
* Dispatch a run for the next action right after one has finished for automations. This speeds up when having an automation with a long interval

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/8.0.0...8.0.1

## 8.0.0 - 2024-04-11

### What's Changed

* A completely new redesign, reflecting the marketing design updates on mailcoach.app and mailcoach.app/self-hosted
* A new automation builder
* and many more small improvements...
* V8 by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1584

> [!IMPORTANT]
Check out the [upgrade guide](https://mailcoach.app/self-hosted/documentation/v8/other/upgrading#content-upgrading-to-v8) on how to upgrade from v7 to v8

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.9.0...8.0.0

## 7.9.0 - 2024-04-10

### What's Changed

* Store attachments & re-attach them when resending a transactional mail by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1582

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.8.2...7.9.0

## 7.8.2 - 2024-04-08

### What's Changed

* Allowed for symfony mailer packages v7 to be installed by @boris-glumpler in https://github.com/spatie/laravel-mailcoach/pull/1580
* Fix the "select all" action on the link clicks component

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.8.1...7.8.2

## 7.8.1 - 2024-04-05

* new asset build

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.8.0...7.8.1

## 7.8.0 - 2024-04-03

### What's Changed

* Adding filament version to the debug component and Github issue body by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1571
* Laravel 11 by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1565
* Load all campaigns in condition component
* Fix searching on campaign name while sorting on email list
* Rely on withoutSendsForCampaign query to set all sends created
* Pick up subscribers that have been added to a segment while sending

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.7.5...7.8.0

## 7.7.5 - 2024-03-20

- Allow an array to be passed to transactional mails so Twig can loop
- New asset build to fix modal height issue

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.7.4...7.7.5

## 7.7.3 - 2024-03-13

### What's Changed

* Segments: remove email list filter by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1562

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.7.2...7.7.3

## 7.7.2 - 2024-02-28

* Don't send campaign report when report recipients is empty
* Fix slide over not being able to scroll
* Fix open & click counts not being shown on subscriber detail

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.7.1...7.7.2

## 7.7.1 - 2024-02-13

### What's Changed

* Use setUser instead of login to support non stateful guards as well by @FinnJanik in https://github.com/spatie/laravel-mailcoach/pull/1538
* Catch the unique constraint violation when trying to add a tag

### New Contributors

* @FinnJanik made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1538

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.7.0...7.7.1

## 7.7.0 - 2024-02-12

### What's Changed

* Migrate Unsubscribe Tests to Expectations API by @shatterproof in https://github.com/spatie/laravel-mailcoach/pull/1534
* Resend a cancelled campaign by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1514

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.6.1...7.7.0

## 7.6.1 - 2024-02-08

### What's Changed

* Fix tests by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1536
* Suppress errors when converting html to text
* Make the ->hasTag check on subscribers more resilient and make the subscriber_id,tag_id index unique

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.6.0...7.6.1

## 7.6.0 - 2024-02-05

### What's Changed

* Subscriber Tags crud by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1498
* Docs/rewrites by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1524
* Force livewire assets injection by @potsky in https://github.com/spatie/laravel-mailcoach/pull/1523
* update snapshots after new mjml version was released by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1528
* Bug fix: twitter redirects by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1527
* Add List-ID header to sent emails
* Take Livewire disk configuration in account
* Fix marking a campaign as sent
* Use correct getModel() method on HasHtmlContent class for Unlayer Editor

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.5...7.6.0

## 6.28.0 - 2024-02-05

### What's Changed

* [V6] Add List-ID header + fix test by @shatterproof in https://github.com/spatie/laravel-mailcoach/pull/1532

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.10...6.28.0

## 7.5.5 - 2024-01-29

* Fix compatibility with laravel-medialibrary v11
* Fix a small bug in the churn rate calculation

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.4...7.5.5

## 7.5.4 - 2024-01-29

### What's Changed

* Allow medialibrary v11 too by @ziming in https://github.com/spatie/laravel-mailcoach/pull/1518
* Fix codemirror editor losing focus
* Fix import/export of stored conditions
* Render mjml when re-rendering content items that use a template

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.3...7.5.4

## 7.5.3 - 2024-01-16

* Fix the condition action component always defaulting to an interval of a day

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.2...7.5.3

## 7.5.2 - 2024-01-11

### What's Changed

* Custom database connection (Rule and Schema) by @davidrushton in https://github.com/spatie/laravel-mailcoach/pull/1511
* Allow custom route namespace by @davidrushton in https://github.com/spatie/laravel-mailcoach/pull/1510

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.1...7.5.2

## 7.5.1 - 2024-01-08

### What's Changed

* Always use custom auth guard by @davidrushton in https://github.com/spatie/laravel-mailcoach/pull/1505
* Record details of Postmark bounces & complaints

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.5.0...7.5.1

## 7.5.0 - 2024-01-03

### What's Changed

* Custom database connection by @davidrushton in https://github.com/spatie/laravel-mailcoach/pull/1490
* Show totals in addition to unique counts in tooltips
* Be smarter about unique times of jobs
* Make templates with full html work when switching them
* Update default Mailgun throttling configuration
* Add status to outbox rows
* Lock creation of tags to prevent duplicates on import

### New Contributors

* @davidrushton made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1490

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.4.3...7.5.0

## 7.4.3 - 2023-12-22

### What's Changed

* Nullable transactional mail replacements by @JordiBontje in https://github.com/spatie/laravel-mailcoach/pull/1499
* Add missing timezone call on some date columns
* Large campaigns create too many sends in advance, so we've lowered this slightly to not fill queues too quickly

### New Contributors

* @JordiBontje made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1499

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.4.2...7.4.3

## 7.4.2 - 2023-12-21

### What's Changed

* Fix the webview script
* Improve transactional subject handling
* Allow replacements in passed in subject
* Fix exporting of segments
* Default webhook's selectable_event_types_enabled to true by @emcro in https://github.com/spatie/laravel-mailcoach/pull/1491
* Translation fix in NL by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1489

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.4.1...7.4.2

## 7.4.1 - 2023-12-18

* Fix duplicating transactional mails with content
* Markdown editor improvements
* Numerous Livewire script & asset improvements

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.4.0...7.4.1

## 7.4.0 - 2023-12-15

### What's Changed

* Sendinblue rebranded to Brevo by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1466

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.3.4...7.4.0

## 7.3.4 - 2023-12-14

* Fix an issue where the subscriber count did not match up with the segment's conditions

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.3.3...7.3.4

## 7.3.3 - 2023-12-14

* Make sure subject from a transactional mail API request gets used instead of template subject
* Don't show queue error on dashboard when using vapor
* Fix Unlayer merge tags not working correctly
* Make Unlayer play nice with split testing

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.3.2...7.3.3

## 7.3.2 - 2023-12-14

### What's Changed

* PostgreSQL compatibility for v7 by @potsky in https://github.com/spatie/laravel-mailcoach/pull/1430
* Phpstan improvements by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1487
* Make sure we url decode values when fuzzy searching in the API endpoints
* Actually convert mjml when sending emails instead of just in the preview

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.3.1...7.3.2

## 7.3.1 - 2023-12-13

* The export page now correctly updates when choosing email lists

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.3.0...7.3.1

## 7.3.0 - 2023-12-13

### What's Changed

* Fix -X parameters, sample UUIDs for consistency, and typos in API Docs by @emcro in https://github.com/spatie/laravel-mailcoach/pull/1484
* Fix clicks exports by @potsky in https://github.com/spatie/laravel-mailcoach/pull/1483
* Remove unnecessary prefixes for DB queries by @bberlijn in https://github.com/spatie/laravel-mailcoach/pull/1460
* The campaigns API endpoints will now render MJML
* Make sure segments filter on email_list_id

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.2.8...7.3.0

## 7.2.8 - 2023-12-04

- fix active_subscribers_count when showing email list via api

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.2.7...7.2.8

## 7.2.7 - 2023-12-01

- fix link to bounces

## 7.2.6 - 2023-12-01

- fix filtering campaign clicks

## 7.2.5 - 2023-12-01

- fix bulk selecting opens

## 7.2.4 - 2023-12-01

- fix saving segment on automation

## 7.2.3 - 2023-11-29

### What's Changed

* handle potential null value on Transactional Emails by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1462

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.2.2...7.2.3

## 7.2.2 - 2023-11-24

### What's Changed

- Bug fix for segments not updating their name (https://github.com/spatie/laravel-mailcoach/commit/6c3520701d63a2aa2815d8fb2da8cacc5229fa24)
- Bug fix for segments not showing all the send campaigns (https://github.com/spatie/laravel-mailcoach/commit/0a41c1691d7a00b2bc6dfc45c952a05d531112d5)

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.2.1...7.2.2

## 7.2.1 - 2023-11-23

### What's Changed

- docs: fix typo by @AlejandroAkbal in https://github.com/spatie/laravel-mailcoach/pull/1449
- Removed subject validation rule of campaign settings form by @lao9s in https://github.com/spatie/laravel-mailcoach/pull/1443

### New Contributors

- @AlejandroAkbal made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1449
- @lao9s made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1443

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.2.0...7.2.1

## 7.2.0 - 2023-11-22

### What's Changed

- html and subject properties are not related to Campaign anymore by @potsky in https://github.com/spatie/laravel-mailcoach/pull/1431
- Add an overview for subscribers in an automation
- Fix subscriber attributes saving incorrectly when saving them from the profile page

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.11...7.2.0

## 7.1.7 - 2023-11-08

- Update remove image button to a link below the image so it works with all sorts of images

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.6...7.1.7

## 7.1.6 - 2023-11-07

- Fix config file not being cachable
- Update publish command
- Fix counts on delivery screen

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.5...7.1.6

## 7.1.5 - 2023-11-06

### What's Changed

- Add file size validation for subscriber imports by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1415

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.4...7.1.5

## 7.1.4 - 2023-11-06

- Fixes Octane compatibility #1414

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.3...7.1.4

## 6.27.10 - 2023-11-06

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.9...6.27.10

## 7.1.3 - 2023-11-06

### What's Changed

- Fix issue with charts data by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1413

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.2...7.1.3

## 7.1.2 - 2023-11-06

- Fix an issue with split testing & embedded webviews for the preview modal

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.1...7.1.2

## 7.1.1 - 2023-11-04

- Use cache for storing the errors during import

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.1.0...7.1.1

## 7.1.0 - 2023-11-04

### What's Changed

- Save import errors to a file instead of the database by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1410
- Re-enable Segment API endpoints for index, show & destroy

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.0.2...7.1.0

## 7.0.2 - 2023-11-02

- Fix wrong controller action in SES wizard
- Fix campaign sent notification count
- Import subscribers job should delete when subscriberImport doesn't exist
- Add livewire styles & scripts to email newsletter pages
- Don't minify html as it causes issues with code snippets
- Remove slow query count on CampaignsComponent
- Don't use ":" in filename for imports

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.0.1...7.0.2

## 7.0.1 - 2023-10-31

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/7.0.0...7.0.1

## 7.0.0 - 2023-10-30

### What's Changed

- Faked transactional mails by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1340
- DRAFT: Condition Builder by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1324
- Mjml by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1355
- Feature/disable webview by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1367
- Refactor to content items by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1376
- Bug/373 unsubscribe by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1369
- DRAFT: Weekdays only by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1370
- Support Substack exports by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1371
- Docs mjml by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1365
- Update docs for v7 by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1379
- Move editor packages into this repository by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1378
- Refactor/move vendor packages by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1382
- Feature/379 import unsubscribed by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1364
- Split testing (correct base branch) by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1387
- Update images in the docs by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1390
- Condition builder changes by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1385
- Docs by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1393
- Customizable utm tags by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1399
- V7 by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1360

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.9...7.0.0

## 6.27.9 - 2023-10-23

### What's Changed

- Make sure DB prefix is taken into account by @bberlijn in https://github.com/spatie/laravel-mailcoach/pull/1396
- Don't include subscribers_csv in the GET endpoint of the subscriberImports API

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.8...6.27.9

## 6.27.8 - 2023-10-22

- Fix an issue with filename not being defined in exports

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.7...6.27.8

## 6.27.7 - 2023-10-18

- Improve the SendCampaignMailsJob to not load in all sending & sent campaigns

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.6...6.27.7

## 6.27.6 - 2023-10-13

- Fix an issue with email list exports from lists that contain special characters in the name

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.5...6.27.6

## 6.27.5 - 2023-10-13

- Subscriber imports with an unsubscribed_at column are now imported as unsubscribed

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.4...6.27.5

## 6.27.4 - 2023-10-13

### What's Changed

- Fix broken counts on "Audience Growth" dashboard chart by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1391
- Fix an issue with settings not being cached when there were none
- Fix #1384 - Fragments should come after query parameters

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.3...6.27.4

## 6.27.3 - 2023-10-02

### What's Changed

- fix: import in SubscribersExportController by @ImJustToNy in https://github.com/spatie/laravel-mailcoach/pull/1381

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.2...6.27.3

## 6.27.2 - 2023-10-02

### What's Changed

- added a link to the Audience section for a sent Campaign by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1358
- feat: faster extra_attributes fetching by using JSON_KEYS by @ImJustToNy in https://github.com/spatie/laravel-mailcoach/pull/1380

### New Contributors

- @ImJustToNy made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1380

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.27.1...6.27.2

## 6.26.0 - 2023-08-18

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.6...6.26.0

## 6.25.6 - 2023-08-17

- Cache ready to use mailers when registering config values

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.5...6.25.6

## 6.25.5 - 2023-08-17

- Add missing wire:key to campaign row

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.4...6.25.5

## 6.25.4 - 2023-08-17

- Make sure uploads are images

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.3...6.25.4

## 6.25.3 - 2023-08-17

- Retrying stuck sends now first calculates how long sends should have been in the queue
- Retrying failed sends from the UI now works with large amount of failed sends
- Lowercase the extension of uploads

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.2...6.25.3

## 6.25.2 - 2023-08-09

- Fix a query issue in MySQL caused by the previous Postgres fix

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.1...6.25.2

## 6.25.1 - 2023-08-09

### What's Changed

- Move DISTINCT ON query to a subquery for Postgres support. by @dannydinges in https://github.com/spatie/laravel-mailcoach/pull/1347
- Fix a bug where Twig was being rendered with escaped quotes (") and subsequently crashing

### New Contributors

- @dannydinges made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1347

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.25.0...6.25.1

## 6.25.0 - 2023-08-07

### What's new

- We now dispatch a `ServingMailcoach` event which allows you to add behaviour only when serving Mailcoach pages

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.6...6.25.0

## 6.24.6 - 2023-08-07

### What's Changed

- Update docs by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1342
- Fix canonical url for email list websites
- Catch invalid "to" address exception
- Stricter validation on emails
- Don't run a query for each hasTag check
- Subscriber import improvements
- Fix an issue with the schedule dropdown being overflow hidden

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.5...6.24.6

## 6.24.5 - 2023-07-25

- Fix turnstile page erroring when using attributes
- Json requests do not render turnstile
- Add missing allowed sorts on segments
- Add export to outbox rows
- Allow sorting on active subscriber count

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.4...6.24.5

## 6.24.4 - 2023-07-07

- Fix a typo causing invalid dates with the date format helper

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.3...6.24.4

## 6.24.3 - 2023-07-07

### What's Changed

- fix date format code so mailcoach can work with postgres by @abhishekbhardwaj in https://github.com/spatie/laravel-mailcoach/pull/1330

### New Contributors

- @abhishekbhardwaj made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1330

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.2...6.24.3

## 6.24.2 - 2023-07-05

- Automation actions now load their statistics after loading the page, which should speed up the UI for automations with a lot of recipients

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.1...6.24.2

## 6.24.1 - 2023-07-05

- Don't count "Production" as a valid value in the debug screen
- Fix open & click counts in the API
- Try parsing twig before saving content, to prevent exceptions when sending the email

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.24.0...6.24.1

## 6.24.0 - 2023-06-23

### What's Changed

- get code sniffer back working by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1317
- replace code sniffer with laravel/pint by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1318
- Phpstan by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1319
- Phpstan by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1320
- Add endpoint for fetching all subscribers with bounces of a campaign by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1323

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.23.0...6.24.0

## 6.23.0 - 2023-05-31

### What's Changed

- Webhook Failure Threshold by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1301
- Fix HTML Preview To Support UTF-8 Charset by @Hussam3bd in https://github.com/spatie/laravel-mailcoach/pull/1315
- Minify html to improve cutoff in gmail

### New Contributors

- @Hussam3bd made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1315

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.22.0...6.23.0

## 6.22.0 - 2023-05-25

### What's Changed

- Feature/multiple reply to by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1309

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.21.0...6.22.0

## 6.21.0 - 2023-05-22

### What's Changed

- Ability to disable website feature by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1314

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.20.3...6.21.0

## 6.20.3 - 2023-05-22

### What's Changed

- Escape brackets in translation file by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1313
- Added translation keys + add to Dutch translation file by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1311

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.20.2...6.20.3

## 6.20.2 - 2023-05-17

- Fix attributes not being replaced in confirmation mails that use a template

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.20.1...6.20.2

## 6.20.1 - 2023-05-16

- Change the timeout of the SendCampaignMailJob to 3 hours (same as unique for)

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.20.0...6.20.1

## 6.20.0 - 2023-05-11

### What's Changed

- Fix missing connections attributes in construct method in some jobs by @Dendreo-Tech-Team in https://github.com/spatie/laravel-mailcoach/pull/1303
- Add turnstile support by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1304

### New Contributors

- @Dendreo-Tech-Team made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1303

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.19.3...6.20.0

## 6.19.3 - 2023-05-02

- Fix issues with FormRequest
- Add a "Subscribe to email list" automation action
- Add support for hiding parts from the webview
- Fix bug with placeholders inside markdown links
- Trim whitespace on imported values

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.18.2...6.19.3

## 6.18.2 - 2023-04-24

- Fix duplicating automations
- Only run automation actions when there is a next one

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.18.1...6.18.2

## 6.18.1 - 2023-04-20

- Don't load Subscriber eloquent models for the dashboard chart
- Check that a campaign is actually sent before using the sent_at on the list website

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.18.0...6.18.1

## 6.18.0 - 2023-04-14

### What's Changed

- Add soft bounces by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1289

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.17.4...6.18.0

## 6.17.4 - 2023-04-13

### What's Changed

- Dutch translations V6 by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1288

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.17.3...6.17.4

## 6.17.3 - 2023-04-11

### What's Changed

- Fix translation key usage by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1281

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.17.2...6.17.3

## 6.17.2 - 2023-04-11

- #1285 - Fix subscriber export not using tag filter
- Don't create conversions for gifs
- Bind segment to configured class

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.17.1...6.17.2

## 6.17.1 - 2023-03-31

- Fix a duplicate uuid issue when duplicating a transactional mail

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.17.0...6.17.1

## 6.17.0 - 2023-03-31

- Allow setting storage url separate from app url
- Fix link to automation mail open subscribers

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.16.2...6.17.0

## 6.16.2 - 2023-03-24

- Exports didn't work with external disks like s3 while using a queue for the exports. Now they do

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.16.1...6.16.2

## 6.16.1 - 2023-03-24

- Fix an extra level being shown in the navigation

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.16.0...6.16.1

## 6.16.0 - 2023-03-24

- You can now view which subscribers clicked on a certain link
- Stuck sends for automation mails will now be retried just like campaign emails
- You can now choose a template when creating an automation mail

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.15.1...6.16.0

## 6.15.1 - 2023-03-21

- Fix an issue with Carbon serialization

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.15.0...6.15.1

## 6.15.0 - 2023-03-17

- We now render twig in the confirmation mail template when a custom transactional mail is chosen and its type is "html"

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.14.12...6.15.0

## 6.14.12 - 2023-03-17

- Cache the full settings model instead of just values to prevent a query on every request

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.14.11...6.14.12

## 6.14.11 - 2023-03-17

### What's Changed

- Error on spanish translation by @chargoy in https://github.com/spatie/laravel-mailcoach/pull/1272
- Count distinct bounces instead of all bounces in case of a duplicate webhook send

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.14.10...6.14.11

## 6.14.10 - 2023-03-15

- allow special characters in twig powered templates

## 6.14.9 - 2023-03-15

- Fix a conflict with Blade & alpine using `@`

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.14.8...6.14.9

## 6.14.8 - 2023-03-14

- sanitise file names of uploads so they are more likely to work in all email clients

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.14.7...6.14.8

## 6.14.7 - 2023-03-14

- fix wording of interval

## 6.14.6 - 2023-03-14

- improve check if unsubscribe url is present

## 6.14.5 - 2023-03-14

- fix RSS feed when campaigns are being sent

## 6.14.4 - 2023-03-14

- display overriden email and reply to on campaign delivery screen

## 6.14.3 - 2023-03-13

- Fixes a small layout issue on the webhook logs overview.
- Handle empty response bodies when storing a webhook log.

## 6.14.2 - 2023-03-13

- handle webhooks events being `null`

## 6.14.1 - 2023-03-10

- Add check to improve compatibility with older configs;
- Small documentation fix;

## 6.14.0 - 2023-03-10

### What's Changed

- Enable webhooks by type by @timvandijck in https://github.com/spatie/laravel-mailcoach/pull/1251
- Merge extra_attributes if already subscribed by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/1263
- Webhook logs by @timvandijck in https://github.com/spatie/laravel-mailcoach/pull/1260

### New Contributors

- @timvandijck made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1251

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.13.1...6.14.0

## 6.13.1 - 2023-02-24

- Fix an issue with large modals overflowing

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.13.0...6.13.1

## 6.13.0 - 2023-02-23

### What's new

- Added API endpoints for tags & segments

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.12.4...6.13.0

## 6.12.4 - 2023-02-20

- Inline html in transactional emails - fix #1223
- Fix search on subscribers with strict mysql

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.12.3...6.12.4

## 6.12.3 - 2023-02-20

- Fix an issue with welcome email links resulting in a 404
- Fix an issue where user fields were empty when editing a user

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.12.2...6.12.3

## 6.12.2 - 2023-02-16

- Revert the enum fix

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.12.1...6.12.2

## 6.12.1 - 2023-02-16

- Fix enum throwing an exception
- Update the spotlight package
- Add a check on subscriber which can be null sometimes
- Fix the format of the tag replacer
- Added the ability to filter automation emails on the automation they're used in

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.12.0...6.12.1

## 6.12.0 - 2023-02-09

- add resubscribe endpoint

## 6.11.0 - 2023-02-08

### What's new

- Added a new `SendWebhookAction` to the automation actions

### What's fixed

- Fix the fallback for the `AddTagsAction`
- Images on email list websites now have a max width of 100%

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.10.3...6.11.0

## 6.10.3 - 2023-02-06

- allow Laravel 10

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.10.2...6.10.3

## 6.10.2 - 2023-02-01

### What's Changed

- Support checking "To" of Sendable by @freekmurze in https://github.com/spatie/laravel-mailcoach/pull/1238

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.10.1...6.10.2

## 6.10.1 - 2023-01-25

- Campaign Jobs should not be unique for only 45 seconds

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.10.0...6.10.1

## 6.10.0 - 2023-01-25

### What's Changed

- Add TagAddedEvent & TagRemovedEvent to webhook by @AlexisSerneels in https://github.com/spatie/laravel-mailcoach/pull/1227
- Don't automatically submit the unsubscribe form anymore
- Render Twig for transactional emails
- Fix an issue where duplicating a transactional email would have the same name

### New Contributors

- @AlexisSerneels made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1227

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.9.0...6.10.0

## 6.9.0 - 2023-01-16

- Don't throw or prevent sending on invalid HTML - but show warnings instead
- Return fields in template API endpoint
- Update migrations to use cascading deletes instead of set null

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.11...6.9.0

## 6.8.11 - 2023-01-12

- Fix the welcome notification using the wrong route parameter

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.10...6.8.11

## 6.8.10 - 2023-01-12

- Add postmark header to transactional sends
- Optimize the SendAutomationMailAction so it only runs for subscribers that need to be run when there is no next action

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.9...6.8.10

## 6.8.9 - 2023-01-10

- Unsaved changes should trigger on any builder update
- Delete pending send when the subscriber was deleted
- Improve reliability of date trigger
- Add attributes to code snippet
- Fix segment class when setting a segment through the API

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.8...6.8.9

## 6.8.8 - 2023-01-04

- Fix an issue where setting the segment through an API call would not save correctly

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.7...6.8.8

## 6.8.7 - 2023-01-03

- Use a cache lock in CreateSubscriberAction

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.6...6.8.7

## 6.8.6 - 2023-01-02

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.5...6.8.6

## 6.8.5 - 2023-01-02

### What's fixed

- Bind to {mailcoachUser} instead of {user} - see #1191
- Fix saving of delimited addresses - see #1191
- Remove log statements - see #1191
- Keep validation for subject - fix #1198
- Fix display & default of date trigger for automations

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.4...6.8.5

## 6.8.4 - 2022-12-29

- make event subscribers configurable (now for real)

## 6.8.3 - 2022-12-29

- make event subscribers configurable

## 6.8.2 - 2022-12-23

- Fix an issue with the AttributeCondition validation

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.8.1...6.8.2

## 6.8.1 - 2022-12-22

- update link to webhook docs

## 6.8.0 - 2022-12-22

### What's Changed

- Attributes and twig by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1186
- Attribute condition by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1188
- Fix link checker when a campaign has a lot of links

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.7.1...6.8.0

## 6.7.1 - 2022-12-12

### What's Changed

- Refactor removeTags method by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/1110
- Make fields optional when creating / updating a campaign with a template via the API (by @freekmurze)

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.7.0...6.7.1

## 6.7.0 - 2022-12-12

### What's Changed

- Add support for PHP 8.2 by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1178

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.2...6.7.0

## 6.6.2 - 2022-12-09

- Fix double password forgot link on login screen

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.1...6.6.2

## 6.6.1 - 2022-12-08

- Fix an issue where the SendTransactionalEmail API endpoint would not call the correct policy method
- Don't mark sends as failed when it's a "Send test" mail
- Add the email list id when sending a campaign test
- Set Mailcoach defaults in all of Mailcoach's routes
- Fixes the default password reset flow that Mailcoach provides

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.0...6.6.1

## 6.6.0 - 2022-12-06

Add support for sending transactional mails using the new [spatie/mailcoach-mailer](https://github.com/spatie/mailcoach-mailer) and [spatie/laravel-mailcoach-mailer](https://spatie/laravel-mailcoach-mailer) packages.

## 6.5.0 - 2022-12-02

### What's new

- Bulk actions! The subscribers table now has selectable rows and bulk actions for deleting and unsubscribing

![image](https://user-images.githubusercontent.com/3626559/205249805-a12e0240-7469-44d0-bd1f-bea9d272cb46.png)

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.2...6.5.0

## 6.4.2 - 2022-11-30

- Use the correct config key for campaign mailer
- The transactional mail api endpoint can now accept attachments

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.1...6.4.2

## 6.4.1 - 2022-11-30

- Sending a test now tries to find a matching subscriber, or creates a temporary in-memory subscriber to handle replacements. It also uses the exact same SendMailAction now that the non-test mails will use.

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.0...6.4.1

## 6.4.0 - 2022-11-28

### What's Changed

- Spanish translation by @chargoy in https://github.com/spatie/laravel-mailcoach/pull/1159

### New Contributors

- @chargoy made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1159

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.3.0...6.4.0

## 6.3.0 - 2022-11-28

- Re-enable Unlayer as a template editor

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.5...6.3.0

## 6.2.5 - 2022-11-28

- Clean sendinblue-feedback webhooks
- Make sure campaigns use UTF-8 encoding
- Retain subject & from when sending a transactional mail through the API

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.4...6.2.5

## 6.2.4 - 2022-11-25

- Set the default guard to the Mailcoach guard in Mailcoach middleware + add it to persistent Livewire middleware

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.3...6.2.4

## 6.2.3 - 2022-11-23

- Use the configured guard when checking auth

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.2...6.2.3

## 6.2.2 - 2022-11-23

- Fix an issue with multiple root elements on the email list onboarding page
- Longer timeout for hiding a notification when the notification is an error

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.1...6.2.2

## 6.2.1 - 2022-11-22

- Use model from config in campaign content controller
- Add email list defaults as placeholder to the new "Sender" fields
- Add precedence bulk header to test mail
- Query & load time improvements with very large lists

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.0...6.2.1

## 6.2.0 - 2022-11-21

### What's new

- Add a way to change Campaign & Automation Mail sender fields from the UI

### What's fixed

- Fix an issue with the subscriber search query #1142
- Fix an issue where the reply to was being set twice
- Fix an issue when importing Mailchimp CSVs

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.2...6.2.0

## 6.1.2 - 2022-11-15

### What's Changed

- Include missing website_theme column in upgrade docs by @flashadvocate in https://github.com/spatie/laravel-mailcoach/pull/1127
- Fixing the incorrect URL to view the purchases on the spatie website by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1128
- Add missing __mc() translation function calls and german translation by @StefanSchuechl in https://github.com/spatie/laravel-mailcoach/pull/1132
- Adding a trailing slash to the input group by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1129
- Disable autosave when the model was saved somewhere else

### New Contributors

- @flashadvocate made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1127

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.1...6.1.2

## 6.1.1 - 2022-11-11

- Update migrations so they don't get published again when upgrading from a v5 project
- Improve the upgrading documentation

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.0...6.1.1

## 6.1.0 - 2022-11-10

### What's new

- Added API endpoints for retrieving sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.0.1...6.1.0

## 6.0.1 - 2022-11-10

- Only enable autosave on Sendable models

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.0.0...6.0.1

## 6.0.0 - 2022-11-09

### Mailcoach v6 is here!

Some notable features:

- Completely new design with improved UX
- Add & manage multiple mailers with different providers with automatic setup
- Show a website archive of your email list's campaigns
- A full-featured template system
- A new & improved Markdown editor
- Send outgoing webhooks
- Improved list insights & charts
- A new "Manage preferences" screen where subscribers can manage the (public) tags attached to them
- A command palette
- Automations can be configured to run more than once for a subscriber
- The ability to override, replace and extend every page of Mailcoach

Check out our [upgrade guide](https://mailcoach.app/docs/self-hosted/v6/upgrading) for more details.

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.2...6.0.0

## 5.11.2 - 2022-11-09

- Add the Sendgrid SMTP header when a transactional mail gets stored

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.1...5.11.2

## 5.11.1 - 2022-11-07

### What's Changed

- Fix command description by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1115
- Add send uuid to transactional mail message headers

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.0...5.11.1

## 5.11.0 - 2022-10-19

### What's new

- Added a `Precedence: Bulk` header to prevent out of office replies

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.7...5.11.0

## 5.10.7 - 2022-10-14

### What's Changed

- Update german translations by @StefanSchuechl in https://github.com/spatie/laravel-mailcoach/pull/1100
- Fix script for retry pending sends by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/1099
- Add maxexceptions https://github.com/spatie/laravel-mailcoach/commit/17daf8339949c068c554083363308f44aae4e0c4

### New Contributors

- @StefanSchuechl made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1100

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.6...5.10.7

## 5.10.6 - 2022-10-10

- Fix edge case where a subscriber that unsubscribes during campaign sending would not remove the send

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.5...5.10.6

## 5.10.5 - 2022-10-10

- Fix an issue where the user agent wasn't available on the debug page

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.4...5.10.5

## 5.10.4 - 2022-10-04

### What's Changed

- Update SendCampaignMailJob.php - fix incorrect use of ?? by @pokmot in https://github.com/spatie/laravel-mailcoach/pull/1094
- Update CreateCampaignSendJob.php - fix so identifier is unique by @pokmot in https://github.com/spatie/laravel-mailcoach/pull/1093

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.3...5.10.4

## 5.10.3 - 2022-09-27

### What's Changed

- Fixes an exception that occurs while exporting by @staninova in https://github.com/spatie/laravel-mailcoach/pull/1077

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.2...5.10.3

## 5.10.2 - 2022-09-27

- make methods protected by default

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.1...5.10.2

## 5.10.1 - 2022-09-01

- Remove Mailcoach overriding the Livewire upload limit

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.0...5.10.1

## 5.10.0 - 2022-08-29

### What's Changed

- Improve security of exports by @freekmurze in https://github.com/spatie/laravel-mailcoach/pull/1058

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.9.0...5.10.0

## 5.9.0 - 2022-08-23

### What's Changed

- Add filesystem configuration section to debug page by @AlexVanderbist in https://github.com/spatie/laravel-mailcoach/pull/1055
- Change default filesystem disks to local by @AlexVanderbist in https://github.com/spatie/laravel-mailcoach/pull/1056

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.2...5.9.0

## 5.8.2 - 2022-08-15

- Don't throttle the campaign job when the campaign is cancelled
- Require 2.2.4 of rate-limited-job-middleware

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.1...5.8.2

## 5.8.1 - 2022-08-13

### What's Changed

- Only export user defined tags in subscriber export by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1050
- Use a rate limiter on the send jobs as well to prevent them being processed too quickly

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.0...5.8.1

## 5.8.0 - 2022-08-07

- Adds a new `mailcoach.timezone` config option for when you want to change Mailcoach's display timezone but not your app's
- Fix a throttling issue when more than 1 queue worker was set up for the send-campaign queue
- Update to spatie/simple-excel v2 as v1 uses an abandoned dependency

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.2...5.8.0

## 5.7.2 - 2022-07-28

### What's Changed

- Fix an issue when scoping on extra attributes
- [Docs] Add vapor .env config details by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1017
- [Docs] Update version num in postmark by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1026

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.1...5.7.2

## 4.17.1 - 2022-07-21

- Fix a bug where child actions in automations were being recreated

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.17.0...4.17.1

## 5.7.1 - 2022-07-11

### What's Changed

- [Docs] Fix imports in docs for v5 by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1011
- Tag: Allow id or model in scoping EmailList by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1013

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.0...5.7.1

## 5.7.0 - 2022-07-01

- Improve imports & exports to be able to work with s3 disks

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.6.0...5.7.0

## 5.6.0 - 2022-06-24

### What's Changed

- Import & export by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1002

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.14...5.6.0

## 5.5.14 - 2022-06-24

### What's Changed

- Fix broken link by @LorenzoSapora in https://github.com/spatie/laravel-mailcoach/pull/998
- Fix an n+1 issue on the segment subscriber index

### New Contributors

- @LorenzoSapora made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/998

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.13...5.5.14

## 5.5.13 - 2022-06-15

### What's changed

- Fix a bug where bounces would calculate statistics too early and cause wrong statistics to be displayed

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.12...5.5.13

## 5.5.12 - 2022-06-15

### What's Changed

- Catch throwable resolving a segmentClass by @masterix21 in https://github.com/spatie/laravel-mailcoach/pull/994
- Fix an issue where statistics recalculating caused invalid data to be displayed

### New Contributors

- @masterix21 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/994

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.11...5.5.12

## 5.5.11 - 2022-06-02

### What's changed

- Fix replacers not replacing url encoded texts #987

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.10...5.5.11

## 5.5.10 - 2022-06-02

### What's Changed

- factories should use custom models when configured by @adamthehutt in https://github.com/spatie/laravel-mailcoach/pull/989

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.9...5.5.10

## 5.5.9 - 2022-05-30

### What's Changed

- Update name of set to be `newsletters` by @jplhomer in https://github.com/spatie/laravel-mailcoach/pull/985
- Make UUID not crash on unsupported databases by @potsky in https://github.com/spatie/laravel-mailcoach/pull/982
- Add "Halted" count to automation overview by @riasvdv

### New Contributors

- @jplhomer made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/985
- @potsky made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/982

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.8...5.5.9

## 5.5.8 - 2022-05-13

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.7...5.5.8

## 5.5.7 - 2022-05-12

## What's Changed

- Optimise removeTags() performance by @JackWH in https://github.com/spatie/laravel-mailcoach/pull/975

## New Contributors

- @JackWH made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/975

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.6...5.5.7

## 5.5.6 - 2022-05-11

## What's changed

- We now dispatch the first action for the subscriber right away when an automation is triggered

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.5...5.5.6

## 5.5.5 - 2022-04-21

- Improve campaign summary screen performance for large campaigns

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.4...5.5.5

## 5.5.4 - 2022-04-20

- Don't mark as sent until all sends are actually sent

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.3...5.5.4

## 5.5.3 - 2022-04-20

## What's Changed

- Update getting-a-license.md by @WouterBrouwers in https://github.com/spatie/laravel-mailcoach/pull/953
- Sort subscribers by updated_at or subscribed_at by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/954
- Fix translations using trans_choice by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/955

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.2...5.5.3

## 5.5.2 - 2022-04-16

## What's Changed

- Fix Redis payload for the CreateSendJob being too large
- Update audience.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/943
- Docs & links improvements by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/944

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.1...5.5.2

## 5.5.1 - 2022-04-12

- Fix summary progress based on created sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.0...5.5.1

## 5.5.0 - 2022-04-11

## What's Changed

- Fix typo in `CampaignLink` docs by @anselmh in https://github.com/spatie/laravel-mailcoach/pull/938
- Dispatch the send right after creating it by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/939

## New Contributors

- @anselmh made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/938

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.7...5.5.0

## 5.4.7 - 2022-04-08

- Move singletons to scoped for better Octane compatibility

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.6...5.4.7

## 5.4.6 - 2022-04-07

- Don't create sends if the campaign is cancelled

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.5...5.4.6

## 5.4.5 - 2022-04-05

## What's Changed

- Make filter fields configurable for transactional mails and campaigns by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/933

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.4...5.4.5

## 5.4.4 - 2022-04-05

## What's Changed

- Refactor creating of sends to jobs for faster processing by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/932

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.3...5.4.4

## 5.4.3 - 2022-04-02

- Fix #918  segment_id in API request
- Fix #916 filter not working with "+"

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.2...5.4.3

## 5.4.2 - 2022-04-01

- Automation actions now also verify that subscribers match the segment before going to the next step

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.1...5.4.2

## 5.4.1 - 2022-03-31

## What's Changed

- Update getting-a-license.md by @WouterBrouwers in https://github.com/spatie/laravel-mailcoach/pull/920
- Add missing translations by @cretueusebiu in https://github.com/spatie/laravel-mailcoach/pull/930

## New Contributors

- @WouterBrouwers made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/920

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.0...5.4.1

## 5.4.0 - 2022-03-21

- Rework how campaigns are sent for better reliability
- Show a schedule overview on the debug screen

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.5...5.4.0

## 5.3.5 - 2022-03-21

- Improve command that dispatches sending campaigns again if the job fails for some reason

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.4...5.3.5

## 5.3.4 - 2022-03-20

- Improve SendCampaignAction query to not include subscribers that already have a send

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.3...5.3.4

## 5.3.3 - 2022-03-18

## What's Changed

- Fix functions using Carbon type declaration instead of CarbonInterface by @zupolgec in https://github.com/spatie/laravel-mailcoach/pull/911
- Hide the create button if the user has no right to create by @nessor in https://github.com/spatie/laravel-mailcoach/pull/910

## New Contributors

- @zupolgec made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/911
- @nessor made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/910

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.2...5.3.3

## 5.3.2 - 2022-03-14

- Fix https://github.com/spatie/laravel-mailcoach/issues/908 - Allow eager loading of tags

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.1...5.3.2

## 5.3.1 - 2022-03-10

- Fix the webhook trigger throwing an exception when you have globally namespaced controllers - #907

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.0...5.3.1

## 5.3.0 - 2022-03-09

## What's Changed

- Add TransactionalMailStored Event to the Docs by @LooxisDev in https://github.com/spatie/laravel-mailcoach/pull/893
- Update upgrading.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/895
- Define new configs to allow default opens/clicks tracking and utm_tag… by @electronick86 in https://github.com/spatie/laravel-mailcoach/pull/900
- Search for uuid first - fix #896
- Don't automatically confirm unsubscribe if send is too recent - fix #905
- Turbo doesn't handle downloads well - fix #903

## New Contributors

- @LooxisDev made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/893
- @electronick86 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/900

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.2.1...5.3.0

## 5.2.1 - 2022-02-21

## What's Changed

- Remove stray ray call
- Fix broken link by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/889
- Update subscribers.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/890

## New Contributors

- @timothyasp made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/889
- @shopbox-support made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/890

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.2.0...5.2.1

## 5.2.0 - 2022-02-15

- Use hotwired/turbo instead of turbolinks

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.1.0...5.2.0

## 5.1.0 - 2022-02-14

## What's new

- Allow campaigns to be filtered on email_list_id

## What's Changed

- Upgrade guide showing wrong table name by @bashgeek in https://github.com/spatie/laravel-mailcoach/pull/886

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.4...5.1.0

## 4.17.0 - 2022-02-14

- Allow campaigns to be filtered on email_list_id

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.2...4.17.0

## 5.0.4 - 2022-02-11

- Fixes some small translation issue

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.3...5.0.4

## 5.0.3 - 2022-02-11

- Minify css & js

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.2...5.0.3

## 5.0.2 - 2022-02-11

## What's Changed

- No longer publish the job_batches migration by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/884
- Update NL translations for V5 by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/883

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.1...5.0.2

## 5.0.1 - 2022-02-11

## What's Changed

- fix typo in docs by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/880
- Remove ray call by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/882
- docs: small improvements by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/881

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.0...5.0.1

## 5.0.0 - 2022-02-10

## What's Changed

- Add support for Laravel 9
- Rework throttling of campaign & automation sends for performance
- Performance improvements
- Drop support for Laravel 8 & below

> **Read the [upgrade guide](https://mailcoach.app/docs/v5/mailcoach/upgrading)!**

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.2...5.0.0

## 4.16.2 - 2022-02-10

- Dispatch calculate statistics when there's a new open or click as well

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.1...4.16.2

## 4.16.1 - 2022-02-08

- Don't dispatch a recalculate statistics job when there are no new sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.0...4.16.1

## 4.16.0 - 2022-02-07

## What's Changed

- Lazy load segments relation by @jpeters8889 in https://github.com/spatie/laravel-mailcoach/pull/874
- Fix subscriber request not resolving a Subscriber in every case #804
- Fix schedule_at not being set on Campaigns through the API #860
- Add uuid to searchable fields #871

## New Contributors

- @jpeters8889 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/874

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.7...4.16.0

## 4.15.7 - 2022-02-03

- Allow for league/html-to-markdown ^5.0

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.6...4.15.7

## 4.15.6 - 2022-02-03

## What's Changed

- fix typo in campaign casts by @StevePorter92 in https://github.com/spatie/laravel-mailcoach/pull/865
- Wrong version removed [docs] by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/867
- Use configuration for action subscriber model by @anyb1s in https://github.com/spatie/laravel-mailcoach/pull/869

## New Contributors

- @StevePorter92 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/865
- @mertasan made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/867
- @anyb1s made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/869

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.5...4.15.6

## 4.15.5 - 2022-01-07

## What's Changed

- Add Turkish Translations by @sineld in https://github.com/spatie/laravel-mailcoach/pull/831

## New Contributors

- @sineld made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/831

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.4...4.15.5

## 4.15.4 - 2022-01-03

## What's Changed

- Fix broken link in postmark.md by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/810
- Update Postmark coupon by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/812
- update documentation by @caneco in https://github.com/spatie/laravel-mailcoach/pull/813
- Add localization function to two strings by @hexation-srl in https://github.com/spatie/laravel-mailcoach/pull/821
- Update nl.json by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/814
- Fixes #793 by @ryanito in https://github.com/spatie/laravel-mailcoach/pull/807
- Fix nested automation actions by @LukeAbell in https://github.com/spatie/laravel-mailcoach/pull/816

## New Contributors

- @caneco made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/813
- @ryanito made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/807

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.3...4.15.4

## 4.15.3 - 2021-12-04

## What's Changed

- Fix schedule command list by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/798
- Automation performance warnings by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/806

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.2...4.15.3

## 4.15.2 - 2021-12-01

## What's Changed

- Support for using factories in 3rd party apps by @lukeraymonddowning in https://github.com/spatie/laravel-mailcoach/pull/794
- fix: remove useless space in filename by @zhanang19 in https://github.com/spatie/laravel-mailcoach/pull/789

## New Contributors

- @lukeraymonddowning made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/794
- @zhanang19 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/789

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.1...4.15.2

## 4.15.1 - 2021-11-16

## What's Changed

- Add method to code example by @willemvb in https://github.com/spatie/laravel-mailcoach/pull/788

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.0...4.15.1

## 4.15.0 - 2021-11-16

## What's Changed

- Remove double Redis requirement in Vapor section by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/764
- Add missing campaign show controller method

## New Contributors

- @dennisvandalen made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/764

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.14.3...4.15.0

## 4.14.3 - 2021-10-11

- fix syntax error (#753)

## 4.14.2 - 2021-10-11

- improve compatibility with Vapor

## 4.14.1 - 2021-10-11

- improve compatibility with Vapor

## 4.14.0 - 2021-10-11

## What's new

- Add trigger model to config (#750)

## 4.13.0 - 2021-10-09

## What's new

- Add Campaign & Automation Mail Link/Click/Open/Unsubscribe to config

## 4.12.0 - 2021-09-30

## What's new

- Added a config option to switch the rate limit driver to either `redis` (default) or `cache`

## 4.11.0 - 2021-09-30

## What's fixed

- Fix the ConditionAction breaking on different locales (#715)
- Fix the subscribed landing view not being accessible without being logged in
- The subject of transactional mail templates will now run replacers (#636)

## What's new

- Use protected props for import subscribers action (#741)
- Add an option to append tags when updating subscribers (#739)

## 4.10.2 - 2021-09-24

- Automation mail from & reply to were being set unexpectedly (#713)
- Fix an incorrect table status message (#692)

## 4.10.1 - 2021-09-20

- Fix an issue with the WaitAction when loading old data.

## 4.10.0 - 2021-09-15

- Automations now continue when they weren't halted for a subscriber and new actions get added.

## 4.9.6 - 2021-09-13

- The `TagRemovedEvent` wasn't being triggered when deleting a tag or syncing tags on a subscriber

## 4.9.5 - 2021-09-06

- bug fix: add missing return type in `registerDeprecatedApiGuard()`

## 4.9.4 - 2021-09-03

- fix actions with seconds (#696)

## 4.9.3 - 2021-09-03

- fix API errors (#707)

## 4.9.2 - 2021-09-03

- Fix email list summary stats

## 4.9.1 - 2021-08-25

- Fix email list summary stats (#691)

## 4.9.0 - 2021-08-23

- Actions can now also receive the `ActionSubscriber` model as a second parameter in their `run` method. The signature of the method has to be `run(Subscriber $subscriber, ?ActionSubscriber $actionSubscriber = null)`.

## 4.8.6 - 2021-08-18

- Fix first_name and last_name in form subscription (#680)

## 4.8.5 - 2021-08-16

- Fix an issue with pagination on queries when `created_at` has the same value. (#671)
- Fix the WaitAction throwing errors with Carbon localization (#657)

## 4.8.4 - 2021-08-06

- Fix an issue with the template edit view

## 4.8.3 - 2021-08-06

- Fix $campaign being referenced instead of $template

## 4.8.2 - 2021-08-06

- Add missing modals to template edit screen

## 4.8.1 - 2021-08-04

- Fix `Action` model not being able to be replaced

## 4.8.0 - 2021-08-04

- Add an `attachSubscriber` to the `Action` model which allows overriding how Subscribers move to the next action

## 4.7.1 - 2021-08-04

- Use the correct table name for ActionSubscriber

## 4.7.0 - 2021-08-03

- Allow multiple automation runs by overwriting an action (#666)

## 4.6.2 - 2021-08-03

- Fix double ampersand encoding (#665)

## 4.6.1 - 2021-07-29

- Remove a stray dd()
- Fix an issue on the automation views when using a custom segment

## 4.6.0 - 2021-07-22

- Allow filter by `email` on the subscribers API endpoint
- Fixed the unsubscribeUrl replacer replacing more than intended (#644)
- Fix segmentSubscriberCount executing too many queries in the same view (#648)

## 4.5.3 - 2021-07-15

- Add missing resolveRouteBinding methods to overridable models (#652)
- Fix various models not pulling from config (#653)

## 4.5.2 - 2021-07-14

- fix incorrect model references (#650)

## 4.5.1 - 2021-06-21

- Dutch translation fix (#643)
- Fix allowed sorts of automation emails (#637)

## 4.5.0 - 2021-06-21

- Add a view for a subscriber's custom attributes (#621)

## 4.4.6 - 2021-06-17

- Remove redundant hasTable check (#620)
- Re-add webview URL to summary page (#613)
- Removed space after translatable string + typo's (#610)
- Call toArray() instead of casting to array (#607)
- Update NL translations (#606)
- Add some missing props to the dateField blade component (#596)
- Fetch models for match from config (#595)
- Fix duplicate transactional template (#593)
- Fix incorrect model references (#591)
- Cache Event based triggers so they don't need to be queried each request

## 4.4.5 - 2021-06-14

- allow schemaless-attributes v2

## 4.4.4 - 2021-06-11

- Fix an issue where automations would keep running for unsubscribed subscribers

## 4.4.3 - 2021-05-31

- upgrade spatie/feed

## 4.4.2 - 2021-05-25

- Automation mails will now use the list from & reply to fields

## 4.4.1 - 2021-05-14

- Fix an issue with empty html throwing an error on the send page
- Fix mails rendering html

## 4.4.0 - 2021-05-12

- add `CheckLicense` command

## 4.3.4 - 2021-05-04

- fix link to docs

## 4.3.3 - 2021-04-27

## 4.3.2 - 2021-04-27

- Fix assets

## 4.3.1 - 2021-04-23

- Fixed an issue when deleting an automationMail that's inside an automation

## 4.3.0 - 2021-04-23

- Only add a `USE INDEX` statement when using MySQL
- Fix FontAwesome Free icons
- Fix an issue on the subscriber received mails view
- Added an extra Tag added trigger when subscribers are confirmed
- Optimized some queries

## 4.2.1 - 2021-04-09

- Fix a MySQL query issue with order by on the email list summary
- Adds missing table prefixes for raw queries #520

## 4.2.0 - 2021-04-07

- save extra attributes via api (#508)
- Fix LinkHasher bug when using custom Campaign model (#505)
- Use v2 of spatie/temporary-directory (#499)
- Make sure subscriber summary on email list is sorted (#516)

## 4.1.0 - 2021-04-06

- add `TransactionalMailStored` event (#507)

## 4.0.13 - 2021-03-29

- fix more mail rendering issues

## 4.0.12 - 2021-03-29

- Fix an issue where html tags were being shown in emails

## 4.0.11 - 2021-03-26

- Fix UTM tags throwing an exception on mailto, tel or other non-url urls

## 4.0.10 - 2021-03-25

- Remove some stray Ray calls

## 4.0.9 - 2021-03-24

- improved config file

## 4.0.8 - 2021-03-24

- Fix links on the automation mail statistics screen

## 4.0.7 - 2021-03-24

- Fix subscribers being added twice to the next action

## 4.0.6 - 2021-03-24

- Fix incorrect link hashes being added as tags

## 4.0.5 - 2021-03-24

- Fix stripping UTM tags from urls without query parameters

## 4.0.4 - 2021-03-24

- Fix an issue with automation counts being incorrect

## 4.0.3 - 2021-03-24

- Fix UTM tags & subscriber tags handling

## 4.0.2 - 2021-03-24

- Fix duplicating automations

## 4.0.1 - 2021-03-24

- display tags that get created for links

## 4.0.0 - 2021-03-24

- added automations
- added transactional mail log
- added transactional mail templates
- refine campaign sending
- revamp of UI
- refactor to domain oriented structure
- internal cleanup
- rewritten docs
- drop support for PHP 7

## 3.10.4 - 2021-02-19

- HTML errors should not prevent loading of links in HTML

## 3.10.3 - 2021-01-26

- Fix stray closing tag

## 3.10.2 - 2021-01-26

- Fix an issue on the campaign details when a subscriber was deleted

## 3.10.1 - 2021-01-18

- Fix welcome mail to use latest subscriber details #426

## 3.10.0 - 2021-01-15

- Allow filling the subject in the UpdateCampaignAction
- Fix an issue on the campaign details when a list was deleted

## 3.9.8 - 2021-01-08

- Fix php constraint

## 3.9.7 - 2021-01-06

- fixed an issue with large segments

## 3.9.6 - 2020-12-28

- fix for empty campaign

## 3.9.5 - 2020-12-17

- Refactor import subscribers action (#395)

## 3.9.4 - 2020-12-16

- Fix an issue with HTML loading in the delivery tab (#384)

## 3.9.3 - 2020-12-16

- trim values from import source (#392)

## 3.9.2 - 2020-12-15

- use laravel-mailcoach for support

## 3.9.1 - 2020-12-15

- improvement for large exports

## 3.9.0 - 2020-12-14

- add `Send` model to config file

## 3.8.1 - 2020-12-10

- Fix a display issue with timezones

## 3.8.0 - 2020-12-10

- Test emails are now prefixed with "[Test]"
- Test emails have a X-Entity-Ref-ID header to prevent threading
- The delivery screen now shows a warning if your message is above 102kb, which could cause clipping in Gmail
- The delivery screen now shows the links found in your campaign, so you can verify they are correct.

## 3.7.0 - 2020-12-09

- add update method on SubscribersController.php (#383)

## 3.6.5 - 2020-12-09

- Debug database version now works on postgres
- Add subject of campaign to preview modal

## 3.6.4 - 2020-12-08

- Make sure the email list graph is scoped by email list

## 3.6.3 - 2020-12-07

- Fix issue with index in multi-tenant setup

## 3.6.2 - 2020-12-07

- ensure index exists when using MySQL

## 3.6.1 - 2020-12-07

- improve number formatting

## 3.6.0 - 2020-12-07

- add list level metrics

## 3.5.0 - 2020-11-30

- add partial for tags (#375)

## 3.4.0 - 2020-11-30

- add support for PHP 8.0

## 3.3.0 - 2020-11-19

- `extra_attributes` & `tags` can now be passed to the create subscribers API endpoint.
- `tags` are now included in Subscriber responses from the API
- Campaign graph now starts from first open if there are opens while the campaign is still sending

## 3.2.13 - 2020-11-17

- fix mails being sent on default queue for campaign batch (#368)

## 3.2.12 - 2020-11-16

- fix variable name in NL translations (#367)

## 3.2.11 - 2020-11-12

- allow medialibrary v9

## 3.2.10 - 2020-11-11

- use `,` as a delimiter for `allowed_form_extra_attributes`

## 3.2.9 - 2020-10-29

- use a custom error message when sending a campaign test email

## 3.2.8 - 2020-10-28

- fix for #358

## 3.2.7 - 2020-10-28

- remove duplicate error message

## 3.2.6 - 2020-10-21

- save attributes on list (#356)

## 3.2.5 - 2020-10-21

- disable autocomplete on search inputs (#353)

## 3.2.4 - 2020-10-19

- fix open and click rates on campaign summery mail (#343)

## 3.2.3 - 2020-10-13

- fix subscription confirmation mail copy

## 3.2.2 - 2020-10-10

- translate the sent settings screen (#338)

## 3.2.1 - 2020-10-05

- fix: only send a welcome mail if the user wasn't already subscribed

## 3.2.0 - 2020-10-05

- add Dutch translations

## 3.1.3 - 2020-10-05

- fix some timezone issues
- fix <html> being added on the campaign html

## 3.1.2 - 2020-10-04

- format other numbers on campaign index view

## 3.1.1 - 2020-10-04

- format send count on campaign index screen

## 3.1.0 - 2020-10-04

- add German translations

## 3.0.6 - 2020-09-30

- improve debug page

## 3.0.5 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 3.0.4 - 2020-09-29

- margin tweak on reply-to

## 3.0.3 - 2020-09-29

- improve campaign index spacing & styles

## 3.0.2 - 2020-09-29

- show segment in campaign overview

## 3.0.1 - 2020-09-29

- don't limit exception message on failed sends

## 3.0.0 - 2020-09-27

- add API
- add used configuration screen
- add reply-to email and name
- add debug page
- improved subscriber import
- use Laravel 8 queued job batches
- use class based factories
- stability improvements
- drop support for Laravel 7

## 2.23.17 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 2.23.16 - 2020-09-29

- don't limit exception message on failed sends

> > > > > > > v2

## 2.23.15 - 2020-09-27

- add `$tries = 1` to `SendCampaignJob`
- add index on `campaign_id, subscriber_id` on the sends table

(see #284)

## 2.23.14 - 2020-09-24

- make sure no unserialize notices are thrown

## 2.23.13 - 2020-09-17

- detach tags when deleting subscriber

## 2.23.12 - 2020-09-08

- fix support for Laravel 8

## 2.23.11 - 2020-09-08

- add support for Laravel 8

## 2.23.10 - 2020-09-06

- allow Guzzle 7

## 2.23.9 - 2020-09-04

- fix an issue where the original doctype was not being kept

## 2.23.8 - 2020-08-28

- fix custom mailable campaign not set

## 2.23.7 - 2020-08-28

- fix extra subscriber attributes that couldn't be null

## 2.23.6 - 2020-08-25

- fix scheduling options

## 2.23.5 - 2020-08-12

- fix Carbon macro to also work on CarbonImmutable

## 2.23.4 - 2020-08-11

- fix Carbon typehints to CarbonInterface

## 2.23.3 - 2020-08-06

- require report recipients if reports are to be sent (#278)

## 2.23.2 - 2020-07-30

- fix a bug with Custom Mailables with subjects that did not send

## 2.23.1 - 2020-07-28

- fix bug that prevents replacement strings from working with a custom mailable (#274)

## 2.23.0 - 2020-07-22

- use POST request to process unsubscribe (#273)

## 2.22.0 - 2020-07-22

- use model cleanup v3

## 2.21.1 - 2020-07-10

- Testing fixes for Custom Mailables

## 2.21.0 - 2020-07-10

- When subscribing an existing subscriber, tags will still be updated.

## 2.20.1 - 2020-07-01

- Fix another regression in the campaign summary

## 2.20.0 - 2020-07-01

- Add a `retry_until_hours` setting to the throttling config

## 2.19.4 - 2020-07-01

- Fix a regression where the campaign summary was not showing the correct messaging

## 2.19.3 - 2020-07-01

- Show a label in the footer when environment isn't `production` or debugging is on

## 2.19.2 - 2020-06-28

- make sure very long error messages from SMTP get processed (#268)

## 2.19.1 - 2020-06-25

- fix getting subscribers by tag (#264)

## 2.19.0 - 2020-06-25

- add config setting to register (or not) blade components (#266)

## 2.18.0 - 2020-06-25

- add German translation (#267)

## 2.17.0 - 2020-06-21

- add support for private filesystems for the import subscribers disk (#263)

## 2.16.0 - 2020-06-21

- add dutch translations (#260)

## 2.15.7 - 2020-06-18

- fix icon alignment in dropdowns (#262)

## 2.15.6 - 2020-06-18

- prevent Stripping of Email Body Element (#261)

## 2.15.5 - 2020-06-17

- translation fixes (#256)

## 2.15.4 - 2020-06-16

- fix theme

## 2.15.2 - 2020-06-14

- fix campaign sending progress bar (#251)

## 2.15.1 - 2020-06-10

- fix typo in one of the translations

## 2.15.0 - 2020-06-10

- add translations (#247)

## 2.14.2 - 2020-06-08

- fix relationship definitions to support custom models (#245)

## 2.14.1 - 2020-06-04

- use custom models in route-model binding (#244)

## 2.14.0 - 2020-06-02

- add support for custom/configurable models (#241)

## 2.13.0 - 2020-05-27

- choose the email list when creating a campaign
- fix error when viewing a campaign with a deleted segment

## 2.12.0 - 2020-05-23

- add support for defining the welcome mail job queue (#238)

## 2.11.8 - 2020-05-14

- Allow both `Carbon` and `CarbonImmutable` to be used (#235)

## 2.11.7 - 2020-05-14

- fix display of campaign summary when the list of the campaign has been deleted

## 2.11.6 - 2020-05-14

- fix for sql_mode=only_full_group_by issue
- fix campaign not being marked as sent with a custom segment

## 2.11.5 - 2020-05-07

- fix for `PrepareEmailHtmlAction` breaking html

## 2.11.4 - 2020-05-06

- fix display of custom segment classes

## 2.11.3 - 2020-05-04

- wrong route in subscribers (#231)

## 2.11.2 - 2020-04-30

- use default action is action class not set in config

## 2.11.1 - 2020-04-30

- fix all filters being active all at once on list pages

## 2.11.0 - 2020-04-30

- add `CampaignReplacer` (#226)

## 2.10.1 - 2020-04-30

- Fix Error htmlspecialchars() in delivery tab
- Fix custom segment display

## 2.10.0 - 2020-04-30

- refactor to Tailwind grid (#228)

## 2.9.1 - 2020-04-29

- fix subjects not getting replaced correctly

## 2.9.0 - 2020-04-27

- add `WebhookCallProcessedEvent` for cleaning up old webhook calls

## 2.8.0 - 2020-04-24

- make models extendible

## 2.7.4 - 2020-04-24

- allow chronos v2

## 2.7.2 - 2020-04-18

- remove links in import confirmation mail

## 2.7.1 - 2020-04-09

- make campaign on mailable nullable (#147)

## 2.7.0 - 2020-04-09

- accept time in register feedback functions

## 2.6.4 - 2020-04-08

- fix custom mailable content

## 2.6.3 - 2020-04-07

- fix broken horses image on confirmation dialog

## 2.6.2 - 2020-04-06

- fix for sending campaigns using custom mailables

## 2.6.1 - 2020-04-06

- format number of mails on confirmation dialog

## 2.6.1 - 2020-04-06

- add view `mailcoach::app.emailLists.layouts.partials.afterLastTab`

## 2.6.1 - 2020-04-06

- add ability to use replacers in the subject of a campaign

## 2.6.1 - 2020-04-06

- fix sorting on email on the outbox screen

## 2.4.6 - 2020-04-03

- fix malformed ampersands when sending

## 2.4.5 - 2020-04-03

- fix malformed ampersands in HTML validation

## 2.4.4 - 2020-04-02

- fix sorting tags by subscriber_count

## 2.4.3 - 2020-04-02

- send campaign sent confirmation only after all mails have been sent

## 2.4.2 - 2020-04-02

- fix invalid route action

## 2.4.1 - 2020-04-01

- improve modal texts

## 2.4.0 - 2020-03-30

- add duplicate segment action

## 2.3.0 - 2020-03-30

- add duplicate template action

## 2.2.2 - 2020-03-30

- improve config file comments

## 2.2.1 - 2020-03-25

- fix js asset url

## 2.2.0 - 2020-03-25

- add `import_subscribers_disk` config option

## 2.1.3 - 2020-03-25

- version assets in blade views

## 2.1.2 - 2020-03-25

- fix icons

## 2.1.1 - 2020-03-23

- fix `ConfirmSubscriberController` not defined when using route caching

## 2.1.0 - 2020-03-22

- add `queue_connection` config option
- add `perform_on_queue.import_subscribers_job` config option

## 2.0.4 - 2020-03-20

- fix error with groupBy in `CampaignOpensQuery`

## 2.0.3 - 2020-03-13

- fix `CreateSubscriberRequest`

## 2.0.2 - 2020-03-11

- Make sure referrer is always set

## 2.0.1 - 2020-03-11

- use `booted` functions instead of `boot` in models
- fix bug where the campaign settings screen did not work when using a custom segment class

## 2.0.0 - 2020-03-10

- add support for Laravel 7
- 
- add support for custom editors
- 
- add ability to use multiple mail configurations
- 
- add ability to send confirmation and welcome mails with a separate mail configuration
- 
- add option to delay welcome mail
- 
- drop support for Laravel 6
- 

## 1.8.0 - 2020-02-27

- add support for Postmark

## 1.7.2 - 2020-02-21

- fix the default from name for campaigns

## 1.7.1 - 2020-02-17

- add support for instantiated segments

## 1.7.0 - 2020-02-17

- add support for instantiated segments. EDIT: due to a merging error, this functionality was not added.

## 1.6.13 - 2020-02-17

- add unique tag on `email_list_id` and `email` in the `mailcoach_subscribers` table

## 1.6.12 - 2020-02-16

- change the mail content fields to have the `text` type in the db

## 1.6.11 - 2020-02-12

- fix encoding of plain text part of sent mails

## 1.6.10 - 2020-02-11

- The `ConvertHtmlToTextAction` will now suppress errors and warnings and try to deliver plain text at all times
- Added the plain text version to the `SendTestMailAction`

## 1.6.9 - 2020-02-10

- `UpdateSubscriberRequest` will now handle lists that have a common email properly

## 1.6.8 - 2020-02-10

- fix `subscribers` view

## 1.6.7 - 2020-02-09

- prevent hidden search field when there are no search results

## 1.6.6 - 2020-02-09

- fix caching latest version

## 1.6.5 - 2020-02-09

- show exception message when html rule fails

## 1.6.4 - 2020-02-08

- fix `SendTypeFilter` file name

## 1.6.3 - 2020-02-07

- make the `url` field of the `mailcoach_campaign_links` table bigger

## 1.6.2 - 2020-02-07

- make latest version checking more robust

## 1.6.1 - 2020-02-06

- change `failure_reason` type from string to text

## 1.6.0 - 2020-02-05

- Add an X-MAILCOACH header to messages sent by Mailcoach

## 1.5.1 - 2020-02-05

- make sure the Mailcoach service provider publishes the medialibrary migration

## 1.5.0 - 2020-02-04

- add `endHead` partial

## 1.4.3 - 2020-02-03

- lower required version for package-versions to ^1.2

## 1.4.2 - 2020-02-03

- fix exception when trying to replace an attribute that is null

## 1.4.1 - 2020-02-03

- make events properties public

## 1.4.0 - 2020-02-02

- add `BounceRegisteredEvent` and `ComplaintRegisteredEvent`

## 1.3.1 - 2020-02-02

- fix `CampaignSend` query class names

## 1.3.0 - 2020-02-01

- add `middleware` config key

## 1.2.3 - 2020-02-01

- fix closing of `strong` tag in numerous views

## 1.2.2 - 2020-01-31

- send mails using default email on email list

## 1.2.1 - 2020-01-31

- fix bug in `ConfirmSubscriberController` (#16)

## 1.2.0 - 2020-01-31

- add `guard` config option

## 1.1.1 - 2020-01-29

- fix FOUC bug in Firefox

## 1.1.0 - 2020-01-29

- move factories to src, so tests of feedback packages can use them

## 1.0.0 - 2020-01-29

- initial release
