import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

enum CustomButtonStyle { filled, outline }

class CustomButton extends StatelessWidget {
  const CustomButton({
    super.key,
    required this.text,
    required this.onPressed,
    this.enabled = true,
    this.style = CustomButtonStyle.filled,
    this.height = 50,
    this.icon,
  });

  final String text;
  final VoidCallback? onPressed;
  final bool enabled;
  final CustomButtonStyle style;
  final double height;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    final active = enabled && onPressed != null;
    final background = switch (style) {
      CustomButtonStyle.filled => active ? ColorConstant.primaryCl : ColorConstant.primaryDisabledCl,
      CustomButtonStyle.outline => ColorConstant.surfaceCl,
    };
    final foreground = style == CustomButtonStyle.outline ? ColorConstant.primaryCl : ColorConstant.white;
    final border = style == CustomButtonStyle.outline
        ? Border.all(color: ColorConstant.primaryCl)
        : Border.all(color: Colors.transparent);

    return SizedBox(
      width: double.infinity,
      height: height,
      child: Material(
        color: background,
        borderRadius: BorderRadius.circular(14),
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: active ? onPressed : null,
          child: DecoratedBox(
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(14), border: border),
            child: Center(
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (icon != null) ...[
                    Icon(icon, color: foreground, size: 20),
                    const SizedBox(width: 8),
                  ],
                  Text(
                    text,
                    style: TextStyle(
                      color: foreground,
                      fontFamily: FontsStyle.bold,
                      fontWeight: FontWeight.w700,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
