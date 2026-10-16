import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/custom_input_fields.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class MobileView extends StatelessWidget {
  const MobileView({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 24),
          child: Column(
            children: [
              const SizedBox(height: 72),
              const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircleAvatar(
                    radius: 18,
                    backgroundColor: ColorConstant.primaryCl,
                    child: Icon(Icons.play_arrow_rounded, color: ColorConstant.white, size: 22),
                  ),
                  SizedBox(width: 10),
                  Text(
                    'MYBAAP',
                    style: TextStyle(
                      fontSize: 28,
                      fontWeight: FontWeight.w800,
                      color: ColorConstant.primaryCl,
                      fontFamily: FontsStyle.bold,
                      letterSpacing: 0.6,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 28),
              const Text(
                'Welcome to MyBaap',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: ColorConstant.white, fontFamily: FontsStyle.bold),
              ),
              const SizedBox(height: 8),
              const Text(
                'Enter your mobile number to continue.',
                textAlign: TextAlign.center,
                style: TextStyle(color: ColorConstant.lightGrayIcCl, fontFamily: FontsStyle.regular, fontSize: 13),
              ),
              const SizedBox(height: 28),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomTextField(
                    labelText: 'Mobile number',
                    hintText: 'Mobile number',
                    controller: auth.mobileController,
                    keyboardType: TextInputType.phone,
                    maxLength: 10,
                    borderColor: ColorConstant.lightGrayCl1,
                    inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                    prefix: const Text(
                      '+91',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: ColorConstant.darkGrayCl,
                        fontFamily: FontsStyle.semiBold,
                      ),
                    ),
                  ),
                  if (auth.error != null) ...[
                    const SizedBox(height: 8),
                    Text(auth.error!, style: const TextStyle(color: ColorConstant.errorCl, fontSize: 12)),
                  ],
                  const SizedBox(height: 30),
                  CustomButton(
                    text: auth.loading ? 'Sending...' : 'Continue',
                    enabled: !auth.loading,
                    onPressed: () async {
                      final ok = await context.read<AuthProvider>().continueWithMobile();
                      if (!ok || !context.mounted) return;
                      Navigator.pushNamed(context, AppRoutes.otp, arguments: context.read<AuthProvider>().pendingMobile);
                    },
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
