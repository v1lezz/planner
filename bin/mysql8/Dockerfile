FROM mysql:8
RUN echo "[mysqld]" >> /etc/mysql/my.cnf
RUN echo  "log_bin_trust_function_creators = 1" >> /etc/mysql/my.cnf
RUN echo "mysql_native_password=ON" >> /etc/mysql/my.cnf
RUN echo "bind-address = 0.0.0.0" >> /etc/mysql/my.cnf