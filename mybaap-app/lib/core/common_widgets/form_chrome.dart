import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';

class WhiteCard extends StatelessWidget {
  const WhiteCard({super.key, required this.child, this.padding});

  final Widget child;
  final EdgeInsetsGeometry? padding;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: ColorConstant.surfaceCl,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: ColorConstant.lightBorderCl),
      ),
      child: child,
    );
  }
}

class MenuIconBox extends StatelessWidget {
  const MenuIconBox({super.key, required this.icon, this.background, this.color});

  final IconData icon;
  final Color? background;
  final Color? color;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: background ?? ColorConstant.menuIconBg,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Icon(icon, size: 22, color: color ?? ColorConstant.primaryCl),
    );
  }
}
