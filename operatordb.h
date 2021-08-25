//
//  operatordb.h
//  operatordb
//
//  Created by Andreas Fink on 23.12.16.
//  Copyright © 2016 Andreas Fink. All rights reserved.
//
// This source is dual licensed either under the GNU GENERAL PUBLIC LICENSE
// Version 3 from 29 June 2007 and other commercial licenses available by
// the author.


/*  this version is remaining for compatibility but should dissapear lateron */
void get_operator_from_imsi(const char *imsi,
                            const char **country, /* this is a 3 character ISO code */
                            const char **organisation,
                            const char **network,
                            const char **abbreviated_name,
                            const char **mcc,
                            const char **mnc,
                            const char **sim,
                            const char **last_update,
                            const char **operator_code);



/*  this version allows passing NULL for all pointers to return data */
void get_operator_from_imsi2(const char *imsi,
                            const char **operator_code,
                            const char **cc2,
                            const char **cc3,
                            const char **country,
                            const char **mcc,
                            const char **mnc,
                            const char **name);
                            
#define HAS_OPERATOR_FROM_IMSI2     1
