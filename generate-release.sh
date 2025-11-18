#!/bin/bash
##################################################
# RentWord Theme - Release Generator
# Genera ZIP con la versión actual del theme
# Auto-actualiza versiones en todos los archivos
##################################################

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔══════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  RentWord Theme - Release Generator     ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════╝${NC}\n"

# Función para incrementar versión
increment_version() {
    local version=$1
    local type=$2
    
    IFS='.' read -ra VER <<< "$version"
    local major=${VER[0]}
    local minor=${VER[1]}
    local patch=${VER[2]}
    
    case $type in
        major)
            major=$((major + 1))
            minor=0
            patch=0
            ;;
        minor)
            minor=$((minor + 1))
            patch=0
            ;;
        patch)
            patch=$((patch + 1))
            ;;
    esac
    
    echo "$major.$minor.$patch"
}

# Función para actualizar versión en archivos
update_version_in_files() {
    local new_version=$1
    local today=$(date +%Y-%m-%d)
    
    echo -e "${BLUE}Actualizando versiones en archivos del tema...${NC}"
    
    # Actualizar functions.php
    if [ -f "functions.php" ]; then
        sed -i.bak "s/define('RENTWORD_VERSION', '[^']*')/define('RENTWORD_VERSION', '$new_version')/" functions.php
        rm -f functions.php.bak
        echo -e "${GREEN}✓ functions.php actualizado${NC}"
    fi
    
    # Actualizar style.css
    if [ -f "style.css" ]; then
        sed -i.bak "s/^Version: .*/Version: $new_version/" style.css
        rm -f style.css.bak
        echo -e "${GREEN}✓ style.css actualizado${NC}"
    fi
    
    # Actualizar readme.txt
    if [ -f "readme.txt" ]; then
        sed -i.bak "s/^Stable tag: .*/Stable tag: $new_version/" readme.txt
        rm -f readme.txt.bak
        echo -e "${GREEN}✓ readme.txt actualizado${NC}"
    fi
    
    echo ""
}

# Detectar versión actual desde functions.php
CURRENT_VERSION=$(grep "define('RENTWORD_VERSION'" functions.php | sed "s/.*'\(.*\)'.*/\1/")

if [ -z "$CURRENT_VERSION" ]; then
    echo -e "${RED}✗ No se pudo detectar la versión en functions.php${NC}"
    read -p "Ingresa la versión manualmente (ej: 4.13.6): " CURRENT_VERSION
fi

echo -e "${GREEN}Versión actual: v${CURRENT_VERSION}${NC}\n"

# Preguntar si desea cambiar la versión
echo -e "${YELLOW}¿Deseas actualizar la versión antes de generar el ZIP?${NC}"
echo "1) Mantener versión actual (${CURRENT_VERSION})"
echo "2) Incrementar PATCH (${CURRENT_VERSION} → $(increment_version $CURRENT_VERSION patch))"
echo "3) Incrementar MINOR (${CURRENT_VERSION} → $(increment_version $CURRENT_VERSION minor))"
echo "4) Incrementar MAJOR (${CURRENT_VERSION} → $(increment_version $CURRENT_VERSION major))"
echo "5) Ingresar versión personalizada"
echo ""
read -p "Selecciona una opción (1-5): " version_option

case $version_option in
    2)
        VERSION=$(increment_version $CURRENT_VERSION patch)
        update_version_in_files $VERSION
        ;;
    3)
        VERSION=$(increment_version $CURRENT_VERSION minor)
        update_version_in_files $VERSION
        ;;
    4)
        VERSION=$(increment_version $CURRENT_VERSION major)
        update_version_in_files $VERSION
        ;;
    5)
        read -p "Ingresa la nueva versión (ej: 4.14.0): " VERSION
        update_version_in_files $VERSION
        ;;
    *)
        VERSION=$CURRENT_VERSION
        echo -e "${GREEN}Usando versión actual: v${VERSION}${NC}\n"
        ;;
esac

# Nombre del archivo ZIP
ZIPNAME="rentword-v${VERSION}.zip"
OUTPUT_DIR="../"
FULL_PATH="${OUTPUT_DIR}${ZIPNAME}"

echo -e "${BLUE}═══════════════════════════════════════════${NC}"
echo -e "${GREEN}Generando release: ${ZIPNAME}${NC}"
echo -e "${BLUE}═══════════════════════════════════════════${NC}\n"

# Verificar si ya existe
if [ -f "$FULL_PATH" ]; then
    echo -e "${YELLOW}El archivo ${ZIPNAME} ya existe${NC}"
    read -p "¿Sobrescribir? (s/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
        echo -e "${YELLOW}Operación cancelada${NC}"
        exit 1
    fi
    rm "$FULL_PATH"
fi

# Crear ZIP
echo -e "${BLUE}Generando ZIP...${NC}"

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
    FILECOUNT=$(unzip -l "$FULL_PATH" | tail -1 | awk '{print $2}')
    
    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║         ZIP CREADO EXITOSAMENTE!         ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}📦 Archivo:${NC}       ${ZIPNAME}"
    echo -e "${BLUE}📏 Tamaño:${NC}        ${FILESIZE}"
    echo -e "${BLUE}📂 Ubicación:${NC}     ${OUTPUT_DIR}"
    echo -e "${BLUE}📄 Archivos:${NC}      ${FILECOUNT}"
    echo -e "${BLUE}🏷️  Versión:${NC}       v${VERSION}"
    echo ""
    echo -e "${GREEN}✓ Listo para distribución!${NC}"
    echo ""
else
    echo -e "${RED}✗ Error al crear el ZIP${NC}"
    exit 1
fi
