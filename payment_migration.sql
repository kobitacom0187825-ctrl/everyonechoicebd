ALTER TABLE orders
 ADD COLUMN payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
 ADD COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
 ADD COLUMN tran_id VARCHAR(80) NULL,
 ADD COLUMN val_id VARCHAR(100) NULL,
 ADD COLUMN gateway_transaction_id VARCHAR(100) NULL,
 ADD COLUMN payment_channel VARCHAR(80) NULL,
 ADD COLUMN payment_response TEXT NULL;
