import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';

class MobileView extends StatefulWidget {
  const MobileView({super.key});

  @override
  State<MobileView> createState() => _MobileViewState();
}

class _MobileViewState extends State<MobileView> {
  final _mobile = TextEditingController();
  String? _error;

  @override
  void dispose() {
    _mobile.dispose();
    super.dispose();
  }

  Future<void> _send() async {
    final mobile = _mobile.text.trim();
    if (!RegExp(r'^[6-9][0-9]{9}$').hasMatch(mobile)) {
      setState(() => _error = 'Enter a valid 10 digit mobile number.');
      return;
    }
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().sendOtp(mobile);
      if (!mounted) return;
      Navigator.pushNamed(context, AppRoutes.otp, arguments: mobile);
    } on ApiException catch (error) {
      setState(() => _error = error.message);
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthProvider>().loading;
    return Scaffold(
      backgroundColor: ColorConstant.white,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 48),
              const Text('MyBaap', style: TextStyle(fontFamily: FontsStyle.bold, fontSize: 32, color: ColorConstant.primaryCl)),
              const SizedBox(height: 8),
              const Text('Enter your mobile number to continue.', style: TextStyle(color: ColorConstant.bodyTextCl)),
              const SizedBox(height: 32),
              TextField(
                controller: _mobile,
                keyboardType: TextInputType.phone,
                maxLength: 10,
                inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                decoration: InputDecoration(
                  counterText: '',
                  hintText: '10 digit mobile number',
                  filled: true,
                  fillColor: ColorConstant.primaryLightCl,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                ),
              ),
              if (_error != null) ...[
                const SizedBox(height: 8),
                Text(_error!, style: const TextStyle(color: ColorConstant.errorCl)),
              ],
              const Spacer(),
              CustomButton(text: loading ? 'Sending...' : 'Send OTP', enabled: !loading, onPressed: _send),
            ],
          ),
        ),
      ),
    );
  }
}
