import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';

class StateMessage extends StatelessWidget {
  const StateMessage({
    super.key,
    required this.title,
    this.subtitle,
    this.onRetry,
  });

  final String title;
  final String? subtitle;
  final VoidCallback? onRetry;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              title,
              textAlign: TextAlign.center,
              style: const TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 16),
            ),
            if (subtitle != null) ...[
              const SizedBox(height: 8),
              Text(
                subtitle!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: ColorConstant.bodyTextCl, fontFamily: FontsStyle.regular),
              ),
            ],
            if (onRetry != null) ...[
              const SizedBox(height: 16),
              CustomButton(text: 'Retry', onPressed: onRetry),
            ],
          ],
        ),
      ),
    );
  }
}
