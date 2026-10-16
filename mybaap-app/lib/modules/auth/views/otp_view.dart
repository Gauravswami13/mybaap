import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class OtpView extends StatelessWidget {
  const OtpView({super.key, required this.mobile});

  final String mobile;

  String get _masked {
    if (mobile.length < 4) return mobile;
    return '${mobile.substring(0, 2)}******${mobile.substring(mobile.length - 2)}';
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final waiting = auth.otpSeconds > 0;
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      body: Column(
        children: [
          SafeArea(
            bottom: false,
            child: Align(
              alignment: Alignment.centerLeft,
              child: IconButton(
                onPressed: () => Navigator.maybePop(context),
                icon: const Icon(Icons.arrow_back_ios_new_rounded, color: ColorConstant.white, size: 18),
              ),
            ),
          ),
          const Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircleAvatar(
                radius: 16,
                backgroundColor: ColorConstant.primaryCl,
                child: Icon(Icons.play_arrow_rounded, color: ColorConstant.white, size: 20),
              ),
              SizedBox(width: 8),
              Text('MYBAAP', style: TextStyle(color: ColorConstant.primaryCl, fontFamily: FontsStyle.bold, fontSize: 22, letterSpacing: 0.6)),
            ],
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(32, 28, 32, 24),
              child: Column(
                children: [
                  const Text(
                    'Verify OTP',
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700, color: ColorConstant.darkGrayCl, fontFamily: FontsStyle.bold),
                  ),
                  const SizedBox(height: 20),
                  Text.rich(
                    TextSpan(
                      text: "We've sent a 6-digit OTP to your Mobile\nNumber $_masked",
                      style: const TextStyle(fontSize: 12, color: ColorConstant.lightGrayIcCl, height: 1.4, fontFamily: FontsStyle.regular),
                      children: [
                        WidgetSpan(
                          alignment: PlaceholderAlignment.middle,
                          child: GestureDetector(
                            onTap: () => Navigator.maybePop(context),
                            child: const Padding(
                              padding: EdgeInsets.only(left: 4),
                              child: Icon(Icons.edit_outlined, size: 15, color: ColorConstant.primaryCl),
                            ),
                          ),
                        ),
                      ],
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 48),
                  _OtpBoxes(controller: auth.otpController),
                  if (auth.error != null) ...[
                    const SizedBox(height: 12),
                    Text(auth.error!, textAlign: TextAlign.center, style: const TextStyle(color: ColorConstant.errorCl, fontSize: 12)),
                  ],
                  const SizedBox(height: 36),
                  CustomButton(
                    text: auth.loading ? 'Checking...' : 'Verify OTP',
                    enabled: !auth.loading,
                    onPressed: () async {
                      final ok = await context.read<AuthProvider>().submitOtp();
                      if (!ok || !context.mounted) return;
                      Navigator.pushNamedAndRemoveUntil(context, AppRoutes.home, (route) => false);
                    },
                  ),
                  const SizedBox(height: 28),
                  GestureDetector(
                    onTap: waiting || auth.loading ? null : () => context.read<AuthProvider>().resendOtp(),
                    child: Text(
                      waiting ? 'Resend OTP in ${auth.otpSeconds}s' : 'Resend OTP',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        fontFamily: FontsStyle.semiBold,
                        color: waiting ? ColorConstant.lightGrayIcCl : ColorConstant.bodyTextCl,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _OtpBoxes extends StatelessWidget {
  const _OtpBoxes({required this.controller});

  final TextEditingController controller;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      decoration: BoxDecoration(
        color: ColorConstant.surfaceCl,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: ColorConstant.lightGrayCl1),
      ),
      child: TextField(
        controller: controller,
        keyboardType: TextInputType.number,
        maxLength: 6,
        textAlign: TextAlign.center,
        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
        style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w700, letterSpacing: 16, color: ColorConstant.darkGrayCl, fontFamily: FontsStyle.bold),
        decoration: const InputDecoration(
          counterText: '',
          hintText: '______',
          hintStyle: TextStyle(letterSpacing: 16, color: ColorConstant.hintTextCl),
          border: UnderlineInputBorder(borderSide: BorderSide(color: ColorConstant.primaryCl)),
          enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: ColorConstant.lightGrayCl1)),
          focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: ColorConstant.primaryCl)),
        ),
      ),
    );
  }
}
