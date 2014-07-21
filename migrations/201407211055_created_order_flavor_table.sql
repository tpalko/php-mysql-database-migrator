create table order_flavors
(id int not null primary key auto_increment,
order_id int not null,
flavor varchar(50) not null,
datecreated timestamp not null);

