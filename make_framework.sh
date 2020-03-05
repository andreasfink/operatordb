#!/bin/bash

FRAMEWORK_DIR=/Library/Application\ Support/FinkTelecomServices/frameworks/operatordb.framework/


install_name_tool -id  "${FRAMEWORK_DIR}/Versions/A/operatordb"  liboperatordb.dylib
/usr/bin/codesign --force --sign 8FF801F4427AE4DFB624CC8D8D41EFD33E8A1BBD -o runtime --timestamp=none liboperatordb.dylib
rm -rf "${FRAMEWORK_DIR}"
mkdir -p "${FRAMEWORK_DIR}/Versions/A/Headers"
mkdir -p "${FRAMEWORK_DIR}/Versions/A/Resources"
cp Info.plist "${FRAMEWORK_DIR}/Versions/A/Resources/Info.plist"
cp VERSION "${FRAMEWORK_DIR}/Versions/A/Resources/VERSION"
cp operatordb.h "${FRAMEWORK_DIR}/Versions/A/Headers/operatordb.h"
cp liboperatordb.dylib "${FRAMEWORK_DIR}/Versions/A/operatordb"

pushd "${FRAMEWORK_DIR}"
ln -s Versions/Current/Headers Headers
ln -s Versions/Current/Resources Resources
ln -s Versions/Current/operatordb operatordb

cd "${FRAMEWORK_DIR}/Versions"
ln -s A Current
popd



