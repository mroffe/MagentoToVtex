# MagentoToVtex
Vtex integration for Magento stores.

http://magentotovtex.primordia.com.br/

MagentoToVtex is a simple project to get catalog from magento and bring to vtex store. Have two branchies in this repo, Master and IncludinSmartFunctions, master is the base and the other is a beta.

#This repo needs
- <a href=http://php.net/downloads.php>PHP 5</a>
- <a href=https://www.mysql.com/downloads/>MYSQL</a>

#Using MagentoToVtex Beta

Visit http://magentotovtex.primordia.com.br/

Fill the form with all data needed.

#Explaining the fields from first screen

<a href=http://magentotovtex.primordia.com.br/><img src=http://s29.postimg.org/dxf30me1j/image.png></a>

<b>Store Name</b>
- Needed to the MagentoConnector Class, is the name of store. Ex: mystore.

<b>Store url</b> 
- Needed to the MagentoConnector Class, is the url of store. Ex: www.mystore.com

<b>User</b>
- Needed to the MagentoConnector Class, is the name used to login in magento webservice. Ex: magentouser

<b>Password</b>
- Needed to the MagentoConnector Class, is the password used to login in magento webservice. Ex: mypass123

<b>DB Host</b>
- Needed to MagentoConnector Class, is the url to magento's database. Ex: 155.234.112.40 or mydb.myhost.com

<b>DB Name</b>
- Needed to MagentoConnector Class, is the name of the magento's database. Ex: mystoredb

<b>User DB</b>
- Needed to MagentoConnector Class, is the name used to log in the magento's database. Ex: myuserdb

<b>Pass DB</b>
- Needed to MagentoConnector Class, is the password used to log in the magento's database. Ex: mypass123

#Explaining the fields from second screen

<a href=http://magentotovtex.primordia.com.br/><img src=http://s15.postimg.org/ssbhk6q1n/image.png></a>

<b>Account Name</b>
- Needed to VtexConnector Class, is the account name from vtex. Can be found in license manager -> accounts. Ex: myvtexstore.

<b>Vtex WebService User</b>
- Needed to VtexConnector Class, is the user used to connect in the vtex's webservice. Ex: myvtexuser.

<b>Vtex Webservice Pass</b>
- Needed to VtexConnector Class, is the password used to connect in the vtex's webservice. Ex: mypass123.

<b>Department ID</b>
- Needed to ProductService Class, is the id from vtex's department. Ex: 1

<b>Category ID</b>
- Needed to ProductService Class, is the id from vtex's category. Ex: 13

###Note: If you use only one level of categories, the department id and category can be the same.

<b>Brand ID</b>
- Needed to ProductService Class, is the id from vtex's brand section. Ex: 2000000

#Explaining the fields from third screen

<a href=http://magentotovtex.primordia.com.br/><img src=http://s21.postimg.org/yzc6284gn/image.png></a>

<b>The sku field</b>
- Put the magento's skus, one per line. If you do not set any, all will be send.

<b>The log</b>
- Is the log of all actions, can be scrolled.

###Note: Click in send button only one time, and wait the finish message in log. You can send again after finish the first process.
