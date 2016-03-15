# <a href=http://magentotovtex.primordia.com.br/>MagentoToVtex</a>
Vtex integration for Magento stores.

<a href=http://magentotovtex.primordia.com.br/><img src=http://s29.postimg.org/dxf30me1j/image.png width=200></a>

http://magentotovtex.primordia.com.br/

MagentoToVtex is a simple project to help you migrate your magento's catalog and to your VTEX store. 

On this repository, there are two branches: *Master* is the base stable code but also there is *IncludinSmartFunctions* as beta code.

#Project requirements
- <a href=http://php.net/downloads.php>PHP 5</a>
- <a href=https://www.mysql.com/downloads/>MYSQL</a>
- <a href=http://php.net/manual/pt_BR/book.imagick.php>PHP imagick (only for beta version)</a> 
```
sudo apt-get install php5-fpm php5-mysql
```
```
sudo apt-get install php5-imagick
```

#Using MagentoToVtex

It's quite simple to use it, just Visit http://magentotovtex.primordia.com.br/ and fill the form.

#Step 1 Fields

<a href=http://magentotovtex.primordia.com.br/><img src=http://s29.postimg.org/dxf30me1j/image.png></a>

<b>Store Name</b>
- Magento store name. *Ex: mystore*

<b>Store url</b> 
- Magento store url. *Ex: www.mystore.com*

<b>User</b>
- Magento login, required to access magento webservice. *Ex: magentouser*

<b>Password</b>
- Magento password, required to access magento webservice. *Ex: mypass123*

<b>DB Host</b>
- Magento database host. *Ex: 155.234.112.40 OR mydb.myhost.com*

<b>DB Name</b>
- Magento database name. *Ex: mystoredb*

<b>User DB</b>
- Magento database user name. *Ex: myuserdb*

<b>Pass DB</b>
- Magento database user password. *Ex: mypass123*

<b>To Do</b>
- Defines data to be migrated. You can send products data or attributes infomations ( like: color, size ...)

#Step 2 Fields

<a href=http://magentotovtex.primordia.com.br/><img src=http://s15.postimg.org/ssbhk6q1n/image.png></a>

<b>Account Name</b>
- VTEX LicenseManager account name. Can be found in **License Manager -> Accounts**. *Ex: myvtexstore*

<b>Vtex WebService User</b>
- VTEX Webservice user name. *Ex: myvtexuser*

<b>Vtex Webservice Pass</b>
- VTEX Webservice user password. *Ex: mypass123*

<b>Department ID</b>
- VTEX department id to where data should be migrated. *Ex: 1*

<b>Category ID</b>
- VTEX category id to where data should be migrated. *Ex: 13*

###Note: If you use only one level of categories, the department id and category can be the same.

<b>Brand ID</b>
- VTEX brand section id. *Ex: 2000000*

#Step 3 Fields

<a href=http://magentotovtex.primordia.com.br/><img src=http://s21.postimg.org/yzc6284gn/image.png></a>

<b>The sku field</b>
- Define Magento's skus to be migrated. If you leave empty all data will be migrated.

<b>The log</b>
- Is the log of all actions, can be scrolled.

###Note: Click in send button only one time, and wait the finish message in log.
