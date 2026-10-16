import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

class GreenAppBar extends StatelessWidget implements PreferredSizeWidget {
  const GreenAppBar({
    super.key,
    this.title,
    this.showBack = true,
    this.actions,
    this.onBack,
  });

  final String? title;
  final bool showBack;
  final List<Widget>? actions;
  final VoidCallback? onBack;

  @override
  Size get preferredSize => const Size.fromHeight(56);

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: ColorConstant.bgCl,
      foregroundColor: ColorConstant.white,
      elevation: 0,
      surfaceTintColor: ColorConstant.bgCl,
      automaticallyImplyLeading: false,
      titleSpacing: 8,
      title: Row(
        children: [
          if (showBack)
            InkWell(
              onTap: onBack ?? () => Navigator.maybePop(context),
              child: const Padding(
                padding: EdgeInsets.symmetric(horizontal: 10),
                child: Icon(Icons.arrow_back_ios_new_rounded, size: 18, color: ColorConstant.white),
              ),
            )
          else
            const SizedBox(width: 13),
          Expanded(
            child: Text(
              title ?? '',
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: ColorConstant.white,
                fontSize: 16,
                fontWeight: FontWeight.w400,
                fontFamily: FontsStyle.regular,
              ),
            ),
          ),
          ...?actions,
        ],
      ),
    );
  }
}
