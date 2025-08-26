CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email_verified BOOLEAN DEFAULT FALSE,
    national_id_nr VARCHAR(20) UNIQUE,
    nationality VARCHAR(50),
    date_of_birth DATE,
    street VARCHAR(100),
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    country VARCHAR(100),
    phone_number VARCHAR(20),
    verification_token VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    reset_token VARCHAR(100),
    reset_token_expires TIMESTAMP,
    profile_uuid UUID ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_users_uuid ON users(uuid);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);

CREATE TABLE documents (
    id SERIAL PRIMARY KEY,
    user_uuid UUID NOT NULL REFERENCES users(uuid) UNIQUE,
    document_uuid UUID NOT NULL UNIQUE,
    document_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_documents_user_uuid ON documents(user_uuid);
CREATE INDEX idx_documents_document_uuid ON documents(document_uuid);

CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    user_uuid UUID NOT NULL REFERENCES users(uuid),
    document_uuid UUID NOT NULL,
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_posts_user_uuid ON posts(user_uuid);
CREATE INDEX idx_posts_document_uuid ON posts(document_uuid);

CREATE TABLE demandas (
    id SERIAL PRIMARY KEY,
    user_uuid UUID NOT NULL REFERENCES users(uuid),
    document_uuid UUID NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    acreedor_nombre VARCHAR(100) NOT NULL,
    acreedor_CIF VARCHAR(20) NOT NULL,
    acreedor_domicilio VARCHAR(100) NOT NULL,
    acreedor_telefono VARCHAR(20) NOT NULL,
    acreedor_FAX VARCHAR(20),
    acreedor_email VARCHAR(100) NOT NULL,
    deudor_nombre VARCHAR(100) NOT NULL,
    deudor_CIF VARCHAR(20) NOT NULL,
    deudor_domicilio VARCHAR(100) NOT NULL,
    deudor_telefono VARCHAR(20) NOT NULL,
    deudor_FAX VARCHAR(20),
    deudor_email VARCHAR(100) NOT NULL,
    factura_numero NUMERIC(10, 2) NOT NULL,
    factura_fecha DATE NOT NULL,
    factura_vencimiento DATE NOT NULL,
    factura_importe_total NUMERIC(10, 2) NOT NULL,
    factura_importe_iva NUMERIC(10, 2) NOT NULL,
    factura_importe_sin_iva NUMERIC(10, 2) NOT NULL,
    concepto TEXT NOT NULL,
    documentos JSONB NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_demandas_user_uuid ON demandas(user_uuid);
CREATE INDEX idx_demandas_document_uuid ON demandas(document_uuid);

CREATE TABLE facturas (
    id SERIAL PRIMARY KEY,
    user_uuid UUID NOT NULL REFERENCES users(uuid),
    document_uuid UUID NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    acreedor_nombre VARCHAR(100) ,
    acreedor_CIF VARCHAR(20) ,
    acreedor_domicilio VARCHAR(100) ,
    acreedor_telefono VARCHAR(20) ,
    acreedor_FAX VARCHAR(20),
    acreedor_email VARCHAR(100) ,
    deudor_nombre VARCHAR(100) ,
    deudor_CIF VARCHAR(20) ,
    deudor_domicilio VARCHAR(100) ,
    deudor_telefono VARCHAR(20) ,
    deudor_FAX VARCHAR(20),
    deudor_email VARCHAR(100) ,
    factura_numero VARCHAR(20) ,
    factura_fecha DATE ,
    factura_vencimiento DATE ,
    factura_importe_total NUMERIC(10, 2) ,
    factura_importe_iva NUMERIC(10, 2) ,
    factura_importe_base NUMERIC(10, 2) ,
    concepto TEXT ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_facturas_user_uuid ON facturas(user_uuid);
CREATE INDEX idx_facturas_document_uuid ON facturas(document_uuid);
CREATE INDEX idx_facturas_acreedor_nombre ON facturas(acreedor_nombre);
CREATE INDEX idx_facturas_deudor_nombre ON facturas(deudor_nombre);
