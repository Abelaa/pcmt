VER 2.1.0 / unreleased
==================

**Added**
* [#575](https://gitlab.com/pcmt/pcmt/-/issues/575): Rules - creation - backend
* [#583](https://gitlab.com/pcmt/pcmt/-/issues/583): Rules - creation - frontend
* [#585](https://gitlab.com/pcmt/pcmt/-/issues/585): Rules - general view
* [#581](https://gitlab.com/pcmt/pcmt/-/issues/581): Rules - permissions
* [#576](https://gitlab.com/pcmt/pcmt/-/issues/576): Rules - listing
* [#577](https://gitlab.com/pcmt/pcmt/-/issues/577): Rules - editing - backend
* [#584](https://gitlab.com/pcmt/pcmt/-/issues/584): Rules - editing - frontend
* [#578](https://gitlab.com/pcmt/pcmt/-/issues/578): Rules - running
* [#600](https://gitlab.com/pcmt/pcmt/-/issues/600): Rules - minor UI issues in the create modal and edit form
* [#580](https://gitlab.com/pcmt/pcmt/-/issues/580): Rules - key attribute 
* [#617](https://gitlab.com/pcmt/pcmt/-/issues/617): Rules - deleting
* [#579](https://gitlab.com/pcmt/pcmt/-/issues/579): Rules - processing

VER 2.0.3 / 2020-09-16
==================

**Added**
* [#590](https://gitlab.com/pcmt/pcmt/-/issues/590): GDSN import in Process Tracker 

**Changed**

**Removed**

**Fixed**
* [#618](https://gitlab.com/pcmt/pcmt/-/issues/618): Build failing - babel__traverse TS error
* [#460](https://gitlab.com/pcmt/pcmt/-/issues/460): Image loading in endless redirect-loop
* [#594](https://gitlab.com/pcmt/pcmt/-/issues/594): Can't approve draft for a new product/product model
* [#596](https://gitlab.com/pcmt/pcmt/-/issues/596): Mysqldump container reporting access denied
* [#599](https://gitlab.com/pcmt/pcmt/-/issues/599): Reference data download problems
* [#601](https://gitlab.com/pcmt/pcmt/-/issues/601): Language codes reference data import problem - better error message
* [#630](https://gitlab.com/pcmt/pcmt/-/issues/630): Fix for creating product model


VER 2.0.2 / 2020-08-21
==================

**Added**
* [#586](https://gitlab.com/pcmt/pcmt/-/issues/586): Create rule class model 

**Fixed**
* [#530](https://gitlab.com/pcmt/pcmt/-/issues/530): Issue with category permits on the draft list
* [#587](https://gitlab.com/pcmt/pcmt/-/issues/587): Bug - GDSN XML import issues
* [#594](https://gitlab.com/pcmt/pcmt/-/issues/594): Can't approve draft for a new product/product model

VER 2.0.1 / 2020-08-12
==================

**Added**
* [#535](https://gitlab.com/pcmt/pcmt/-/issues/535): Clicking on associated product navigates to that product
* [#547](https://gitlab.com/pcmt/pcmt/-/issues/547): Implement category access - Drafts bulk reject and single draft reject
* [#537](https://gitlab.com/pcmt/pcmt/-/issues/537): Populate packaging heirarchy table from GTIN import
* [#539](https://gitlab.com/pcmt/pcmt/-/issues/539): Update GDSN CIN mapping to reflect demo-data attribute changes
* [#552](https://gitlab.com/pcmt/pcmt/-/issues/552): Display version of PCMT in UI
* [#562](https://gitlab.com/pcmt/pcmt/-/issues/562): Create automated tests for PCMT External API (category permissions) - GET products

**Fixed**
* [#554](https://gitlab.com/pcmt/pcmt/-/issues/554): Invalid warning after draft bulk reject
* [#548](https://gitlab.com/pcmt/pcmt/-/issues/548): Mst supplier export don’t show value in summary column
* [#549](https://gitlab.com/pcmt/pcmt/-/issues/549): Bulk approve - limit access according to permissions in the process tracker view
* [#560](https://gitlab.com/pcmt/pcmt/-/issues/560): Version building error
* [#561](https://gitlab.com/pcmt/pcmt/-/issues/561): Trying to reject a draft without object causes error


VER 2.0.0 / 2020-07-17
==================

**Permissions to products basing on categories and user groups**

* [#440](https://gitlab.com/pcmt/pcmt/-/issues/440): Setting permissions for specific categories - frontend
* [#437](https://gitlab.com/pcmt/pcmt/-/issues/437): Setting permissions for specific categories - persistence
* [#490](https://gitlab.com/pcmt/pcmt/-/issues/490): Implement category access - bulk approve of drafts
* [#492](https://gitlab.com/pcmt/pcmt/-/issues/492): Implement category access - view the products list
* [#493](https://gitlab.com/pcmt/pcmt/-/issues/493): Implement category access - view the list of product models and variants
* [#491](https://gitlab.com/pcmt/pcmt/-/issues/491): Implement category access - bulk delete of products
* [#438](https://gitlab.com/pcmt/pcmt/-/issues/438): Implement category access - edit product through drafts system
* [#504](https://gitlab.com/pcmt/pcmt/-/issues/504): Limit view of drafts only to those that user has "EDIT" access according to category permissions
* [#508](https://gitlab.com/pcmt/pcmt/-/issues/508): Handling single delete of product / product model
* [#486](https://gitlab.com/pcmt/pcmt/-/issues/486): Implement category permissions in External API - product endpoints
* [#523](https://gitlab.com/pcmt/pcmt/-/issues/523): Implement category permissions in External API - product models endpoints
* [#522](https://gitlab.com/pcmt/pcmt/-/issues/522): Implement category permissions in External API - product model endpoints
* [#521](https://gitlab.com/pcmt/pcmt/-/issues/521): Implement category permissions in External API - products endpoints
* [#520](https://gitlab.com/pcmt/pcmt/-/issues/520): Add External API credentials to custom dataset
* [#442](https://gitlab.com/pcmt/pcmt/-/issues/442): Implement actual access rights based on categories in import jobs
* [#485](https://gitlab.com/pcmt/pcmt/-/issues/485): Implement actual access rights based on categories in export jobs
* [#526](https://gitlab.com/pcmt/pcmt/-/issues/526): Implement actual access rights based on categories in export jobs - mst supplier export
* [#524](https://gitlab.com/pcmt/pcmt/-/issues/524): Implement actual access rights based on categories in export jobs - products
* [#525](https://gitlab.com/pcmt/pcmt/-/issues/525): Implement actual access rights based on categories in export jobs - product models
* [#439](https://gitlab.com/pcmt/pcmt/-/issues/439): Create automatic functional test for access rights for category
* [#533](https://gitlab.com/pcmt/pcmt/-/issues/533): Clear title for category permissions clone checkbox

**Added**
* [#518](https://gitlab.com/pcmt/pcmt/-/issues/518): GS1-GDSN import - ignore tradeItem if found product is of other family than GS1-GDSN
* [#538](https://gitlab.com/pcmt/pcmt/-/issues/538): Automatically categorize GDSN imports into the GS1-GDSN Trade Items category

**Fixed**
* [#507](https://gitlab.com/pcmt/pcmt/-/issues/507): Table attribute breaks when upgrading
* [#472](https://gitlab.com/pcmt/pcmt/-/issues/472): Attribute description not visible in draft
* [#519](https://gitlab.com/pcmt/pcmt/-/issues/519): Add more info in bulk delete report
* [#531](https://gitlab.com/pcmt/pcmt/-/issues/531): Summary column shouldn't be empty on the Process tracker view
* [#455](https://gitlab.com/pcmt/pcmt/-/issues/455): Make two (reference data) functional test passing on CI


VER 1.1.5 / 2020-05-28
==================

**Fixed**
* [#495](https://gitlab.com/pcmt/pcmt/-/issues/495): Fix for migrations error

VER 1.1.4 / 2020-05-14
==================

**Fixed**
* [#471](https://gitlab.com/pcmt/pcmt/-/issues/471): Product import generic validation error
* [#463](https://gitlab.com/pcmt/pcmt/-/issues/463): Fix problem with `make`
* [#448](https://gitlab.com/pcmt/pcmt/-/issues/448): Empty drafts are created when importing products with concatenated attribute
* [#462](https://gitlab.com/pcmt/pcmt/-/issues/462): Fix table attribute plugin to work with PCMT Drafts 

VER 1.1.3 / 2020-04-14
==================

**Added**
* [#43](https://gitlab.com/pcmt/pcmt/-/issues/43): User can create Job Instances to download/import reference data. 
* [#417](https://gitlab.com/pcmt/pcmt/-/issues/417): Block simultaneous draft edit by 2 or more users
* [#323](https://gitlab.com/pcmt/pcmt/-/issues/323): Add a second locale to PCMTs demo dataset.
* [#380](https://gitlab.com/pcmt/pcmt/-/issues/380): User get information about parallel changes in the same draft when trying to save outdated draft. 
* [#113](https://gitlab.com/pcmt/pcmt/-/issues/113): Concatenated Attribute are available for many attributes.
* [#334](https://gitlab.com/pcmt/pcmt/-/issues/334): List of GS1 ISO 3166 are available as reference data.
* [#361](https://gitlab.com/pcmt/pcmt/-/issues/361): Integration test working with Selenium are available by new command (dev-test-selenium).
* [#302](https://gitlab.com/pcmt/pcmt/-/issues/302): Functional testing using Behat are available by new command (dev-test-api).
* [#351](https://gitlab.com/pcmt/pcmt/-/issues/351): Mutation tests are available by a new command (dev-test-mutation).
* [#328](https://gitlab.com/pcmt/pcmt/-/issues/328): French language is available in the user interface. 
* [#236](https://gitlab.com/pcmt/pcmt/-/issues/236): Pipeline fails when code coverage on branch is below threshold set in gitlab.
* [#236](https://gitlab.com/pcmt/pcmt/-/issues/236): Phpunit tests code coverage report are automatically generated.

**Changed**
* [#381](https://gitlab.com/pcmt/pcmt/-/issues/381): Change GDSN country attributes to use country code reference data.
* [#385](https://gitlab.com/pcmt/pcmt/-/issues/385): Change GDSN language attributes to use language code reference data
* [#384](https://gitlab.com/pcmt/pcmt/-/issues/384): Add Language Codes (ISO-639) to Reference Data pull
* [#277](https://gitlab.com/pcmt/pcmt/-/issues/277): GDSN-Queue (e2open) Import goes through Drafts/Approvals.
* [#314](https://gitlab.com/pcmt/pcmt/-/issues/314): Messages when draft is already approved or rejected.

**Removed**
* [#277](https://gitlab.com/pcmt/pcmt/-/issues/277): Dependencies in PcmtDraftBundle on PcmtCoreBundle - it can works separately.
* [#340](https://gitlab.com/pcmt/pcmt/-/issues/340): pcmt:custom-dataset:csv:create command.
* [#317](https://gitlab.com/pcmt/pcmt/-/issues/317): Connection between phpstorm and unit test code coverage report.

**Fixed**
* [#404](https://gitlab.com/pcmt/pcmt/-/issues/404): Search by names didn't work for attributes with ISO codes
* [#406](https://gitlab.com/pcmt/pcmt/-/issues/406): When trying to save invalid values ​​the screen loads endlessly
* [#403](https://gitlab.com/pcmt/pcmt/-/issues/403): Only for the RH family it is possible to change the locale while editing the product
* [#224](https://gitlab.com/pcmt/pcmt/-/issues/224): Displaying "missing required attributes" in a draft for a product model isn't working properly
* [#350](https://gitlab.com/pcmt/pcmt/-/issues/350): TYPE SPECIFIC PARAMETERS are not saved in the Concatenated Attribute.
* [#353](https://gitlab.com/pcmt/pcmt/-/issues/353): Draft list disappears when an attribute from family of the draft is removed.
* [#196](https://gitlab.com/pcmt/pcmt/-/issues/196): User get unclear information about missing required field in a draft.
* [#346](https://gitlab.com/pcmt/pcmt/-/issues/346): User can't create a draft of a new product.
* [#330](https://gitlab.com/pcmt/pcmt/-/issues/330): Places and messages where there is no translation.
* [#316](https://gitlab.com/pcmt/pcmt/-/issues/316): GDSN Effective Date Time field not mapped
* [#317](https://gitlab.com/pcmt/pcmt/-/issues/317): 'make dev-clean' command is broken and doesn't work.
* [#305](https://gitlab.com/pcmt/pcmt/-/issues/305): Duplicated alert about validation violation when user try to approve wrong filled draft.
* [#297](https://gitlab.com/pcmt/pcmt/-/issues/297): ERD pipeline job is timing out.


VER 1.1.2 / 2020-02-25
==================

**Added**
* [#303](https://gitlab.com/pcmt/pcmt/-/issues/303): Shell script for load data from file into database (dev-import-sql).

**Changed**
* [#236](https://gitlab.com/pcmt/pcmt/-/issues/236): Exclude DependencyInjection and upgrades from code coverage report.

**Removed**
* none

**Fixed**
* [#298](https://gitlab.com/pcmt/pcmt/-/issues/298): No message to user when approving draft already approved by bulk approve.
* [#300](https://gitlab.com/pcmt/pcmt/-/issues/300): Bulk action show wrong number of selected drafts.



VER 1.1.1 / 2020-02-20
==================

**Added**
* [#272](https://gitlab.com/pcmt/pcmt/-/issues/272): Client messages improvement in the draft system.

**Changed**
* none

**Removed**
* none

**Fixed**
* [#294](https://gitlab.com/pcmt/pcmt/-/issues/294): Bulk approve doesn't work after using process of data restoration.
* [#290](https://gitlab.com/pcmt/pcmt/-/issues/290): User cannot approve a draft after adding a concatenated attribute to the family.