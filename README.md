# PDF-Certificate-generator
Generate and Validate PDF Certificates

Simple and lightweight with PHP (7.4) implementation of generation certificate of course completion. Storage - MySQL DB. PDF file generator - TCPDF Library. Front-end: HTML Form on Bootstrap (+ datepicker) 

App - project private dir
public_html - project public dir

## Requirements 

Have to be created / installed


### 1. Create App/config.php - MySQL DB connect settings & php configs

Minimum required defining constants:
```
  define('__DB_HOST__', 'localhost'); 
  define('__DB_NAME__', 'your_DB_Name'); 
  define('__DB_USER__', 'your_DB_User');
  define('__DB_PWD__',  'your_DB_password');
```


### 2. Use App/Core/dump.sql - DB Table "Certificates" Dump

    For instalation only


### 3. Create App/Libs/TCPDF

    TCPDF Library with gratitude to @tecnickcom (c)
   
    Please, get from https://github.com/tecnickcom/tcpdf
