Magento2 add a cms page link to menu
========================



## Who is developing this module ? 


This module is built by Amit Bera, [Magento StackExchange Moderator](https://magento.stackexchange.com/users/4564/amit-bera?tab=profile) & Magento Certified Developer & Consultant.

@Contact Me https://www.amitbera.com/contact/

## Feature of this extension



### Add cms page to Magento default menu at left and right section


![Menu](docs/static/frontend-enabled.png)

**Manage Pages section and Sort Order from admin Configuration.**

Ability add cms pages and it's sort Order from Admin System Configuration.

![Admin Setting](docs/static/enable%20Setting.png)


## Installation Process

1. Download the extension .zip file from github https://github.com/devamitbera/magento2-add-cms-pages-to-menu.
2. Copy the extension  to the `{magento2-root-dir}/app/code/DevBera/CmsLinkToMenu` OR Clone the files from github repo   to `{magento2-root-dir}/app/code/DevBera/CmsLinkToMenu` using command 

`git clone https://github.com/devamitbera/magento2-add-cms-pages-to-menu.git {magento2-root-dir}/app/code/DevBera/CmsLinkToMenu -f`

3. Run the following series of command from SSH console of your server:
`php bin/magento module:enable DevBera_CmsLinkToMenu  --clear-static-content`
`php bin/magento setup:upgrade`


## Standards & Code Quality

Built on top of Magento2, our module respects all its prerequisites and code quality rules.

