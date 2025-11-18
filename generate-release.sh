#!/bin/bash
##################################################
# RentWord Pro - Release Generator
# Genera ZIP con la versión actual del theme
##################################################

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}🏖️  RentWord Pro - Release Generator${NC}\n"

# Detectar versión desde functions.php
VERSION=$(grep "define('RENTWORD_VERSION'" functions.php | sed "s/.*'\(.*\)'.*/\1/")

if [ -z "$VERSION" ]; then
    echo -e "${YELLOW}⚠️  No se pudo detectar la versión en functions.php${NC}"
    read -p "Ingresa la versión manualmente (ej: 3.1.0): " VERSION
fi

echo -e "${GREEN}📌 Versión detectada: v${VERSION}${NC}"

# Nombre del archivo ZIP
ZIPNAME="rentword-pro-v${VERSION}.zip"
OUTPUT_DIR="../"
FULL_PATH="${OUTPUT_DIR}${ZIPNAME}"

# Verificar si ya existe
if [ -f "$FULL_PATH" ]; then
    echo -e "${YELLOW}⚠️  El archivo ${ZIPNAME} ya existe${NC}"
    read -p "¿Sobrescribir? (s/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
        echo -e "${YELLOW}❌ Operación cancelada${NC}"
        exit 1
    fi
    rm "$FULL_PATH"
fi

# Crear ZIP
echo -e "${BLUE}📦 Generando ZIP...${NC}"

cd ..
zip -r "$ZIPNAME" rentword \
    -x "*.git*" \
    -x "*.DS_Store" \
    -x "*node_modules*" \
    -x "*.log" \
    -x "*README_old.md" \
    -x "*generate-release.sh" \
    -x "*.zip" \
    -q

cd rentword

# Verificar creación
if [ -f "$FULL_PATH" ]; then
    FILESIZE=$(ls -lh "$FULL_PATH" | awk '{print $5}')
    echo -e "${GREEN}✅ ZIP creado exitosamente!${NC}"
    echo -e "${GREEN}📄 Archivo: ${ZIPNAME}${NC}"
    echo -e "${GREEN}📊 Tamaño: ${FILESIZE}${NC}"
    echo -e "${GREEN}📍 Ubicación: ${OUTPUT_DIR}${NC}"
    
    # Contar archivos
    FILECOUNT=$(unzip -l "$FULL_PATH" | tail -1 | awk '{print $2}')
    echo -e "${GREEN}📁 Total archivos: ${FILECOUNT}${NC}"
    
    echo -e "\n${BLUE}🎉 ¡Listo para distribución!${NC}"
else
    echo -e "${YELLOW}❌ Error al crear el ZIP${NC}"
    exit 1
fi
