# Use official PHP image with Apache
FROM php:8.2-apache

# Copy backend files to /var/www/html
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Create uploads directory
RUN mkdir -p /var/www/html/uploads/contratti && chmod 775 /var/www/html/uploads/contratti

# Enable mod_rewrite for .htaccess
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]