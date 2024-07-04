# Proyecto CodeIgniter 4 - CRUD de Productos

## Descripción

Este proyecto es una aplicación CRUD de productos desarrollada con CodeIgniter 4. La aplicación utiliza JSON como almacenamiento en lugar de una base de datos. También se ha integrado SweetAlert para mostrar alertas amigables al usuario.

## Requisitos

- PHP 8.2
- Composer
- Node.js y npm

## Instalación

### Clonar el repositorio

Primero, clona el repositorio:

git clone https://github.com/alanrtaiariol/ecomm.git
cd ecomm

#  Instalar dependencias PHP

composer install

# Permisos
 sudo chown -R www-data:www-data /var/www/ecomm


##  Para correr los test ejecutar por consola

vendor/bin/phpunit tests/ProductModel/ProductModelTest.php 


