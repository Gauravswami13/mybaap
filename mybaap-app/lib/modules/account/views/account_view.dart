import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';

class AccountView extends StatelessWidget {
  const AccountView({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Profile', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: ListView(
        children: [
          ListTile(
            title: Text(auth.user?.displayName ?? 'MyBaap', style: const TextStyle(fontFamily: FontsStyle.semiBold)),
            subtitle: Text(auth.user?.mobile ?? ''),
          ),
          const Divider(),
          _tile(context, 'Privacy Policy', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'privacy-policy')),
          _tile(context, 'Terms', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'terms')),
          _tile(context, 'FAQ', () => Navigator.pushNamed(context, AppRoutes.faq)),
          _tile(context, 'Purchase History', () => Navigator.pushNamed(context, AppRoutes.purchases)),
          const SizedBox(height: 24),
          ListTile(
            title: const Text('Delete Account', style: TextStyle(fontFamily: FontsStyle.medium, color: ColorConstant.errorCl)),
            onTap: () => _confirmDelete(context),
          ),
        ],
      ),
    );
  }

  Future<void> _confirmDelete(BuildContext context) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete account'),
        content: const Text('This is permanent. Your name and mobile number will be removed, and your subscription will end.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Delete', style: TextStyle(color: ColorConstant.errorCl)),
          ),
        ],
      ),
    );
    if (confirmed != true || !context.mounted) return;
    try {
      await context.read<AuthProvider>().deleteAccount();
      if (!context.mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, AppRoutes.mobile, (route) => false);
    } on ApiException catch (error) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(error.message)));
    }
  }

  Widget _tile(BuildContext context, String title, VoidCallback onTap) {
    return ListTile(
      title: Text(title, style: const TextStyle(fontFamily: FontsStyle.medium)),
      trailing: const Icon(Icons.chevron_right, color: ColorConstant.primaryCl),
      onTap: onTap,
    );
  }
}
