todo
====
A Symfony project created on August 10, 2016, 10:37 pm.

# Application Install #

### Clone the project ###
 - git clone [https://github.com/kedwards/todo.git](https://github.com/kedwards/todo.git "Todo Project") 

### Install required dependecies with composer ###
 - php composer install

### Configure DB parameters for the application ###
 - database_host: **127.0.0.1**
 - database_port: **3306**
 - database_name: **todo**
 - database_user: **todo_admin**
 - database_password: **todo_admin_pass**
 - mail_transport: **smtp**
 - mailer_host: **127.0.0.1**
 - mailer_user: **null**
 - mailer_password: **null**

### Make folders available for www-data:admin users ###
##### Note: The below permissions are very permissive ;-) #####
 - cd todo
 - sudo su -c 'chown -R www-data:admin app/ src/'
 - sudo su -c 'find app/ src/ -type d -exec chmod 775 {} \;'                  
 - sudo su -c 'find app/ src/ -type f -exec chmod 664 {} \;'

### Create Application db & users ###
 - From command line run:
  - mysql -u root -p < app/build/db_init.sql
	
### Run Migrations and Schema update ###
 1. php app/console doctrine:migrations:migrate
 2. php app/console doctrine:schema:update --force

### Configure vhost to point to <web_app_directory>/web	###
    server {                                                                                                         
        listen 80;                                                                                                   
        server_name todo.kncedwards.com;                                                                             
        return 301 https://$host$request_uri;                                                                        
    }                                                                                                                

    server {                                                                                                         
        listen 443 ssl;                                                                                              
        server_name todo.kncedwards.com;                                                                             

        root /var/www/src/todo/web;                                                                            
        index index.html index.php app_dev.php app.php;                                                              

        client_max_body_size 20M;                                                                                    

        ssl on;                                                                                                      
        ssl_dhparam /etc/ssl/certs/dhparam.pem;                                                                      

        ssl_certificate /etc/letsencrypt/live/todo.kncedwards.com/fullchain.pem;                                     
        ssl_certificate_key /etc/letsencrypt/live/todo.kncedwards.com/privkey.pem;                                   

        ssl_session_timeout 5m;                                                                                      
        ssl_protocols TLSv1 TLSv1.1 TLSv1.2;                                                                         
        ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:!SSLv3:+EXP;                                       
        ssl_prefer_server_ciphers on;                                                                                
	
        ssl_stapling on;                                                                                             
        ssl_stapling_verify on;                                                                                      
	
        ssl_trusted_certificate /etc/letsencrypt/live/todo.kncedwards.com/chain.pem;                                 
        resolver 8.8.8.8 8.8.4.4 valid=86400;                                                                        
        resolver_timeout 10;                                                                                         
	
        location = /favicon.ico {                                                                                    
            log_not_found off;                                                                                       
	    access_log off;                                                                                          
        }                                                                                                            
	
        location / {                                                                                                 
	    try_files $uri $uri/ /app_dev.php?$args;                                                                 
	    autoindex on;                                                                                            
        }                           
	
        location ~ ^/app_dev\.php(/|$) {                                                                               
            fastcgi_pass unix://var/run/php/php7.0-fpm.sock;                                                           
            fastcgi_index index.php;                                                                                   
            fastcgi_split_path_info ^(.+?\.php)(/.*)$;                                                                 
            fastcgi_read_timeout 150;                                                                                  
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;                                          
            fastcgi_param PATH_INFO $fastcgi_script_name;                                                              
            fastcgi_param APPLICATION_ENV development;                                                                 
            include fastcgi_params;                                                                                    
        }

        location ~ \.php$ {                                                                                            
            return 404;                                                                                                  
        }                                                                                                              
    }

### Browse Application ###
** change host to match the given server_name **

[https://todo.kncedwards.com/todo](http://todo.kncedwards.com/todo  "Browse Todo App")
