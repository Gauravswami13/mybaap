import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';

class OtpView extends StatefulWidget {
  const OtpView({super.key, required this.mobile});

  final String mobile;

  @override
  State<OtpView> createState() => _OtpViewState();
}

class _OtpViewState extends State<OtpView> {
  final _otp = TextEditingController();
  String? _error;
  int _seconds = 30;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _startTimer();
  }

  void _startTimer() {
    _timer?.cancel();
    setState(() => _seconds = 30);
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_seconds <= 1) {
        timer.cancel();
        setState(() => _seconds = 0);
      } else {
        setState(() => _seconds -= 1);
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _otp.dispose();
    super.dispose();
  }

  Future<void> _verify() async {
    if (_otp.text.trim().length != 6) {
      setState(() => _error = 'Enter the 6 digit OTP.');
      return;
    }
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().verifyOtp(widget.mobile, _otp.text.trim());
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, AppRoutes.home, (route) => false);
    } on ApiException catch (error) {
      setState(() => _error = error.message);
    }
  }

  Future<void> _resend() async {
    try {
      await context.read<AuthProvider>().sendOtp(widget.mobile);
      _startTimer();
    } on ApiException catch (error) {
      setState(() => _error = error.message);
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthProvider>().loading;
    return Scaffold(
      backgroundColor: ColorConstant.white,
      appBar: AppBar(title: const Text('Verify OTP', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Code sent to ${widget.mobile}', style: const TextStyle(color: ColorConstant.bodyTextCl)),
            const SizedBox(height: 8),
            const Text('Local testing OTP is 123456.', style: TextStyle(color: ColorConstant.hintTextCl, fontSize: 12)),
            const SizedBox(height: 24),
            TextField(
              controller: _otp,
              keyboardType: TextInputType.number,
              maxLength: 6,
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              style: const TextStyle(fontFamily: FontsStyle.bold, fontSize: 24, letterSpacing: 8),
              decoration: InputDecoration(
                counterText: '',
                hintText: '••••••',
                filled: true,
                fillColor: ColorConstant.primaryLightCl,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
              ),
            ),
            if (_error != null) ...[
              const SizedBox(height: 8),
              Text(_error!, style: const TextStyle(color: ColorConstant.errorCl)),
            ],
            const SizedBox(height: 16),
            TextButton(
              onPressed: _seconds == 0 && !loading ? _resend : null,
              child: Text(_seconds == 0 ? 'Resend OTP' : 'Resend in ${_seconds}s'),
            ),
            const Spacer(),
            CustomButton(text: loading ? 'Checking...' : 'Verify', enabled: !loading, onPressed: _verify),
          ],
        ),
      ),
    );
  }
}
