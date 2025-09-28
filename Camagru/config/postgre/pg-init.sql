CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT gen_random_uuid() UNIQUE NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    send_notifications BOOLEAN DEFAULT TRUE,
    password TEXT NOT NULL,
    two_factor_secret TEXT,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_number NUMERIC(6),
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
    is_active BOOLEAN DEFAULT TRUE,
    verification_token VARCHAR(100),
    token_created_at TIMESTAMP,
    profile_uuid UUID ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_users_uuid ON users(uuid);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);

CREATE TABLE pending_registrations (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    validation_token NUMERIC(6),
    token_validated BOOLEAN DEFAULT FALSE,
    token_expires TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL '15 minutes'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_pending_registrations_token ON pending_registrations(validation_token);
CREATE INDEX idx_pending_registrations_username ON pending_registrations(username);

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
    document_uuid UUID,
    status VARCHAR(50) DEFAULT 'pending',
    acreedor_nombre VARCHAR(100) NOT NULL,
    acreedor_cif VARCHAR(20) NOT NULL,
    acreedor_domicilio VARCHAR(100) NOT NULL,
    acreedor_telefono VARCHAR(20),
    acreedor_fax VARCHAR(20),
    acreedor_email VARCHAR(100),
    acreedor_representante_legal VARCHAR(100),
    deudor_nombre VARCHAR(100) NOT NULL,
    deudor_cif VARCHAR(20) NOT NULL,
    deudor_domicilio VARCHAR(100) NOT NULL,
    deudor_telefono VARCHAR(20),
    deudor_fax VARCHAR(20),
    deudor_email VARCHAR(100),
    deudor_representante_legal VARCHAR(100),
    importe_total_deuda NUMERIC(10, 2) NOT NULL,
    lista_facturas JSONB NOT NULL,
    juzgado TEXT,
    origen_deuda TEXT,
    documentos_adjuntos TEXT,
    solicitud_medidas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_demandas_user_uuid ON demandas(user_uuid);
CREATE INDEX idx_demandas_document_uuid ON demandas(document_uuid);

CREATE TABLE facturas (
    id SERIAL PRIMARY KEY,
    user_uuid UUID NOT NULL REFERENCES users(uuid),
    document_uuid UUID NOT NULL,
    id_demanda INTEGER REFERENCES demandas(id),
    status VARCHAR(50) DEFAULT 'pending',
    acreedor_nombre VARCHAR(100) ,
    acreedor_cif VARCHAR(20) ,
    acreedor_domicilio VARCHAR(100) ,
    acreedor_telefono VARCHAR(20) ,
    acreedor_fax VARCHAR(20),
    acreedor_email VARCHAR(100) ,
    deudor_nombre VARCHAR(100) ,
    deudor_cif VARCHAR(20) ,
    deudor_domicilio VARCHAR(100) ,
    deudor_telefono VARCHAR(20) ,
    deudor_fax VARCHAR(20),
    deudor_email VARCHAR(100) ,
    factura_numero VARCHAR(20) ,
    factura_fecha DATE ,
    factura_vencimiento DATE ,
    factura_importe_total NUMERIC(10, 2) ,
    factura_importe_iva NUMERIC(10, 2) ,
    factura_importe_base NUMERIC(10, 2) ,
    contrato_numero VARCHAR(20),
    contrato_uuid UUID,
    concepto TEXT ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_facturas_user_uuid ON facturas(user_uuid);
CREATE INDEX idx_facturas_document_uuid ON facturas(document_uuid);
CREATE INDEX idx_facturas_acreedor_nombre ON facturas(acreedor_nombre);
CREATE INDEX idx_facturas_deudor_nombre ON facturas(deudor_nombre);
CREATE INDEX idx_facturas_demanda_numero ON facturas(id_demanda);