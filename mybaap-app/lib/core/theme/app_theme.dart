import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

class AppTheme {
  static ThemeData light() {
    final base = ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: ColorConstant.primaryCl,
        primary: ColorConstant.primaryCl,
        surface: ColorConstant.white,
      ),
      scaffoldBackgroundColor: ColorConstant.bgCl,
    );
    return base.copyWith(
      textTheme: base.textTheme.apply(
        fontFamily: FontsStyle.regular,
        bodyColor: ColorConstant.darkGrayCl,
        displayColor: ColorConstant.darkGrayCl,
      ),
      appBarTheme: const AppBarTheme(
        elevation: 0,
        backgroundColor: ColorConstant.white,
        foregroundColor: ColorConstant.darkGrayCl,
        systemOverlayStyle: SystemUiOverlayStyle(
          statusBarColor: Colors.transparent,
          statusBarIconBrightness: Brightness.dark,
        ),
      ),
    );
  }
}
