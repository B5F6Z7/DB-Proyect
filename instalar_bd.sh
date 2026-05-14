#!/bin/bash
# ============================================================
# Script de instalación de la BD — Notas Parciales
# Uso: bash instalar_bd.sh
# ============================================================

DB_NAME="notas_parciales"
DB_USER="postgres"

echo "========================================"
echo " Instalando BD: $DB_NAME"
echo "========================================"

# 1. Crear la base de datos (ignorar error si ya existe)
echo "[1/3] Creando base de datos..."
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;" 2>/dev/null \
    && echo "      Base de datos creada." \
    || echo "      Base de datos ya existe, continuando..."

# 2. Ejecutar schema
echo "[2/3] Creando tablas e índices..."
sudo -u postgres psql -d $DB_NAME -f "$(dirname "$0")/schema.sql"
if [ $? -ne 0 ]; then
    echo "ERROR en schema.sql — abortando."
    exit 1
fi

# 3. Ejecutar triggers
echo "[3/3] Creando disparadores..."
sudo -u postgres psql -d $DB_NAME -f "$(dirname "$0")/triggers.sql"
if [ $? -ne 0 ]; then
    echo "ERROR en triggers.sql — abortando."
    exit 1
fi

# Opcional: cargar datos de prueba
read -p "¿Cargar datos de prueba? (s/n): " resp
if [[ "$resp" == "s" || "$resp" == "S" ]]; then
    sudo -u postgres psql -d $DB_NAME -f "$(dirname "$0")/seed.sql"
    echo "Datos de prueba cargados."
fi

echo ""
echo "========================================"
echo " Instalación completada exitosamente."
echo " Conectar con: psql -U postgres -d $DB_NAME"
echo "========================================"
