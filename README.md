# Operatordb #

This is a very simple library to get from IMSI to mobile operator information. The 
source is generated out of a database of mobile operators and is organized in a digit
tree for high speed translation.

There is only one C function *get_operator_from_imsi* doing all the work.


	./configure

	make

	make install

	_
will build it normally.
For codesigning under Mac OS, define APPLICATION_CERT to your Developer ID certificate number
	
	export APPLICATION_CERT={your-codesign-identity}
	make
	./make_framework.sh


if you want to modify the database and regenerate it, do the following:

1. create a mysqldb and import the operatodb.sql database in it
2. copy dp.php-example to db.php and edit it accordingly
3. type:
    
	make generate

	make
    
	make install

this will regenerate the operatordb.c out of the database and recompile it.
