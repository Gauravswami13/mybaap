import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';

class CustomTextField extends StatelessWidget {
  const CustomTextField({
    super.key,
    this.hintText = '',
    this.controller,
    this.prefix,
    this.trailing,
    this.validator,
    this.inputFormatters,
    this.keyboardType,
    this.readOnly = false,
    this.labelRequired = false,
    this.labelText,
    this.onChanged,
    this.onTap,
    this.maxLength,
    this.textCapitalization,
    this.borderColor,
    this.fillColor,
  });

  final String hintText;
  final TextEditingController? controller;
  final Widget? prefix;
  final Widget? trailing;
  final String? Function(String?)? validator;
  final List<TextInputFormatter>? inputFormatters;
  final TextInputType? keyboardType;
  final bool readOnly;
  final bool labelRequired;
  final String? labelText;
  final ValueChanged<String>? onChanged;
  final VoidCallback? onTap;
  final int? maxLength;
  final TextCapitalization? textCapitalization;
  final Color? borderColor;
  final Color? fillColor;

  @override
  Widget build(BuildContext context) {
    final border = borderColor ?? ColorConstant.lightBorderCl;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (labelText != null)
          Row(
            children: [
              Text(
                labelText!,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: ColorConstant.darkGrayCl,
                  fontFamily: FontsStyle.semiBold,
                ),
              ),
              if (labelRequired)
                const Text(
                  ' *',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: ColorConstant.redLSecCl,
                    fontFamily: FontsStyle.semiBold,
                  ),
                ),
            ],
          ),
        if (labelText != null) const SizedBox(height: 4),
        TextFormField(
          readOnly: readOnly,
          textCapitalization: textCapitalization ?? TextCapitalization.none,
          textInputAction: TextInputAction.done,
          controller: controller,
          onChanged: onChanged,
          onTap: onTap,
          keyboardType: keyboardType ?? TextInputType.name,
          maxLength: maxLength,
          validator: validator,
          inputFormatters: inputFormatters,
          style: const TextStyle(
            fontWeight: FontWeight.w400,
            fontSize: 14,
            color: ColorConstant.darkGrayCl,
            fontFamily: FontsStyle.medium,
          ),
          decoration: InputDecoration(
            counterText: '',
            fillColor: fillColor ?? ColorConstant.surfaceCl,
            filled: true,
            contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 12),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: border)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: border)),
            disabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: border)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: border)),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide: const BorderSide(color: ColorConstant.errorCl),
            ),
            hintText: hintText,
            hintStyle: const TextStyle(
              fontSize: 12,
              color: ColorConstant.hintTextCl,
              fontWeight: FontWeight.w400,
              fontFamily: FontsStyle.regular,
            ),
            isDense: true,
            prefixIcon: prefix == null ? null : Padding(padding: const EdgeInsets.symmetric(horizontal: 12), child: prefix),
            prefixIconConstraints: const BoxConstraints(minWidth: 0, minHeight: 0),
            suffixIcon: trailing == null ? null : Padding(padding: const EdgeInsets.symmetric(horizontal: 12), child: trailing),
            suffixIconConstraints: const BoxConstraints(minWidth: 0, minHeight: 0),
            errorStyle: const TextStyle(
              color: ColorConstant.errorCl,
              fontWeight: FontWeight.w400,
              fontSize: 10,
              fontFamily: FontsStyle.regular,
            ),
          ),
        ),
      ],
    );
  }
}
