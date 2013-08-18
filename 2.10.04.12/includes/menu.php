<?php
/***************************************************************************
 *                               menu.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/

$menuvar = array(

/*###############################
#       Menu Variables          #
###############################*/
'ADMIN' => 'index.php?p=admin',
'APPOINTMENTS' => 'index.php?p=admin&s=appointments',
'CATEGORIES' => 'index.php?p=admin&s=categories',
'CLIENTS' => 'index.php?p=admin&s=clients',
'DOWNLOADS' => 'index.php?p=admin&s=downloads',
'EMAILINVOICE' => 'index.php?p=invoices&action=emailinvoice',
'EMAILORDER' => 'index.php?p=orders&action=emailorder',
'GRAPHS' => 'index.php?p=admin&s=graphs',
'HOME' => 'index.php',
'INVOICES' => 'index.php?p=admin&s=invoices',
'INVOICEPAYMENT' => 'index.php?p=invoices&action=paymenthistory',
'LOGIN' => 'index.php?p=login',
'LOGOUT' => 'index.php?p=logout',
'MENUS' => 'index.php?p=admin&s=menus',
'MYDOWNLOADS' => 'index.php?p=mydownloads',
'MYINVOICES' => 'index.php?p=myinvoices',
'MYNOTES' => 'index.php?p=mynotes',
'MYORDERS' => 'index.php?p=myorders',
'NOTES' => 'index.php?p=admin&s=notes',
'ORDERS' => 'index.php?p=admin&s=orders',
'ORDERPAYMENT' => 'index.php?p=orders&action=paymenthistory',
'PRODUCTS' => 'index.php?p=admin&s=products',
'REGISTER' => 'index.php?p=register',
'REPORTS' => 'index.php?p=admin&s=reports',
'SETTINGS' => 'index.php?p=admin&s=settings',
'THEMES' => 'index.php?p=admin&s=themes',
'USERS' => 'index.php?p=admin&s=users',
'VIEWINVOICE' => 'index.php?p=invoices&action=viewinvoice',
'VIEWORDER' => 'index.php?p=orders&action=vieworder',
'VIEWREPORT' => 'index.php?p=admin&s=reports&action=viewreport',


/*###############################
#       Dead Variable          #
###############################*/
'dead' => 'dead.php'
);
?>