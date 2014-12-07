#!/bin/bash

SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do
    DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
    SOURCE="$(readlink "$SOURCE")"
    [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE"
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

INSTALL_DIR="$DIR/../opencart"
UPLOAD_DIR="$DIR/upload"

FILES=(
    "catalog/model/module/berg.php"
    "catalog/controller/module/berg.php"
    "catalog/view/theme/default/template/module/berg.tpl"
    "catalog/view/theme/default/stylesheet/berg.css"
    "admin/language/russian/module/berg.php"
    "admin/view/template/module/berg.tpl"
    "admin/controller/module/berg.php"
)

for FILE in "${FILES[@]}"; do
    DIR="$( dirname "$FILE" )"

    if [ ! -d "$UPLOAD_DIR/$DIR" ]; then
      mkdir -p "$UPLOAD_DIR/$DIR"
    fi

    echo "> $FILE"
    cp -ar "$INSTALL_DIR/$FILE" "$UPLOAD_DIR/$FILE"
done

echo "All ready, Sir!"
