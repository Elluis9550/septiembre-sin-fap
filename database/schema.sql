-- =========================================================
-- Septiembre Sin Fap — Schema PostgreSQL (Neon)
-- =========================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id              SERIAL PRIMARY KEY,
    nombre          VARCHAR(100)        NOT NULL,
    username        VARCHAR(50)         NOT NULL UNIQUE,
    password        VARCHAR(255)        NOT NULL, -- password_hash()
    activo          BOOLEAN             NOT NULL DEFAULT TRUE,
    dias            INTEGER             NOT NULL DEFAULT 0,
    fecha_registro  TIMESTAMPTZ         NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS reportes (
    id              SERIAL PRIMARY KEY,
    usuario_id      INTEGER             NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    fecha           DATE                NOT NULL,
    resultado       VARCHAR(12)         NOT NULL CHECK (resultado IN ('sobrevivio', 'falle')),
    fecha_registro  TIMESTAMPTZ         NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_reporte_usuario_fecha UNIQUE (usuario_id, fecha)
);

CREATE TABLE IF NOT EXISTS caidas (
    id              SERIAL PRIMARY KEY,
    usuario_id      INTEGER             NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    fecha           DATE                NOT NULL,
    dias            INTEGER             NOT NULL,
    rango           VARCHAR(50)         NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_reportes_usuario ON reportes(usuario_id);
CREATE INDEX IF NOT EXISTS idx_reportes_fecha ON reportes(fecha);
CREATE INDEX IF NOT EXISTS idx_caidas_usuario ON caidas(usuario_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_activo ON usuarios(activo);

-- Usuario de ejemplo (password: "demo1234")
-- INSERT INTO usuarios (nombre, username, password) VALUES
-- ('Demo', 'demo', '$2y$10$replace_with_real_hash');
