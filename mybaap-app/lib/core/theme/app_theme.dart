import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

class AppTheme {
  static ThemeData light() {
    final base = ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      colorScheme: const ColorScheme.dark(
        primary: ColorConstant.primaryCl,
        surface: ColorConstant.surfaceCl,
        secondary: ColorConstant.primaryCl,
      ),
      scaffoldBackgroundColor: ColorConstant.bgCl,
      bottomSheetTheme: const BottomSheetThemeData(elevation: 0, backgroundColor: ColorConstant.surfaceCl),
    );
    return base.copyWith(
      textTheme: base.textTheme.apply(
        fontFamily: FontsStyle.regular,
        bodyColor: ColorConstant.darkGrayCl,
        displayColor: ColorConstant.darkGrayCl,
      ),
      appBarTheme: const AppBarTheme(
        elevation: 0,
        backgroundColor: ColorConstant.bgCl,
        foregroundColor: ColorConstant.darkGrayCl,
        systemOverlayStyle: SystemUiOverlayStyle(
          statusBarColor: Colors.transparent,
          statusBarIconBrightness: Brightness.light,
        ),
      ),
    );
  }
}
