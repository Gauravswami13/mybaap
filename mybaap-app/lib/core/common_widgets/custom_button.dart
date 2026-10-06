import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

class CustomButton extends StatelessWidget {
  const CustomButton({
    super.key,
    required this.text,
    required this.onPressed,
    this.enabled = true,
  });

  final String text;
  final VoidCallback? onPressed;
  final bool enabled;

  @override
  Widget build(BuildContext context) {
    final active = enabled && onPressed != null;
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: Material(
        color: active ? ColorConstant.primaryCl : ColorConstant.primaryDisabledCl,
        borderRadius: BorderRadius.circular(14),
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: active ? onPressed : null,
          child: Center(
            child: Text(
              text,
              style: const TextStyle(
                color: ColorConstant.white,
                fontFamily: FontsStyle.bold,
                fontSize: 14,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
