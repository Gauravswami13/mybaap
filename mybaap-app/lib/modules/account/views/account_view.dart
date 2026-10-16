import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/app_image.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/form_chrome.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/account/providers/share_provider.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:provider/provider.dart';

class AccountView extends StatefulWidget {
  const AccountView({super.key, this.embedded = false});

  final bool embedded;

  @override
  State<AccountView> createState() => _AccountViewState();
}

class _AccountViewState extends State<AccountView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AuthProvider>().loadProfile();
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final name = auth.user?.displayName ?? 'MyBaap';
    final subscription = auth.subscription.isActive
        ? 'Subscribed${auth.subscription.planName == null ? '' : ' · ${auth.subscription.planName}'}'
        : 'No active subscription';
    final expiry = auth.subscription.endDate;

    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: widget.embedded ? null : const GreenAppBar(title: 'Account'),
      body: ListView(
        padding: EdgeInsets.fromLTRB(16, widget.embedded ? 36 : 16, 16, 16),
        children: [
          if (widget.embedded) ...[
            const Text('Profile', style: TextStyle(color: ColorConstant.darkGrayCl, fontFamily: FontsStyle.bold, fontSize: 22)),
            const SizedBox(height: 12),
          ],
          InkWell(
            onTap: () => Navigator.pushNamed(context, AppRoutes.editProfile),
            borderRadius: BorderRadius.circular(12),
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
              decoration: BoxDecoration(
                color: ColorConstant.surfaceCl,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: ColorConstant.lightBorderCl),
                boxShadow: [
                  BoxShadow(
                    offset: const Offset(0, 2),
                    blurRadius: 12,
                    color: ColorConstant.primaryCl.withValues(alpha: 0.17),
                  ),
                ],
              ),
              child: Row(
                children: [
                  ProfileAvatar(name: name, photoUrl: auth.user?.photoUrl),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          name,
                          style: const TextStyle(
                            color: ColorConstant.darkGrayCl,
                            fontWeight: FontWeight.w500,
                            fontFamily: FontsStyle.medium,
                            fontSize: 16,
                          ),
                        ),
                        Text(
                          subscription,
                          style: const TextStyle(
                            color: ColorConstant.lightGrayIcCl,
                            fontWeight: FontWeight.w400,
                            fontFamily: FontsStyle.regular,
                            fontSize: 12,
                          ),
                        ),
                        if (expiry != null && expiry.isNotEmpty)
                          Text(
                            'Till $expiry',
                            style: const TextStyle(
                              color: ColorConstant.lightGrayIcCl,
                              fontFamily: FontsStyle.regular,
                              fontSize: 10,
                            ),
                          ),
                        const Text(
                          'Edit profile',
                          style: TextStyle(
                            color: ColorConstant.primaryCl,
                            fontWeight: FontWeight.w400,
                            fontFamily: FontsStyle.regular,
                            fontSize: 11,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Main Menu',
            style: TextStyle(
              color: ColorConstant.darkGrayCl,
              fontWeight: FontWeight.w500,
              fontFamily: FontsStyle.medium,
              fontSize: 13,
            ),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: ColorConstant.surfaceCl,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: ColorConstant.lightBorderCl),
            ),
            child: Column(
              children: [
                _item(Icons.card_membership_outlined, 'Plans', () => Navigator.pushNamed(context, AppRoutes.subscription)),
                _item(Icons.receipt_long_outlined, 'Purchase history', () => Navigator.pushNamed(context, AppRoutes.purchases)),
                _item(Icons.notifications_none_rounded, 'Notifications', () => Navigator.pushNamed(context, AppRoutes.notifications)),
                _item(Icons.quiz_outlined, 'FAQ', () => Navigator.pushNamed(context, AppRoutes.faq)),
                _item(Icons.share_outlined, 'Share App', () => context.read<ShareProvider>().shareApp()),
                _item(Icons.description_outlined, 'Privacy policy', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'privacy-policy')),
                _item(Icons.article_outlined, 'Terms', () => Navigator.pushNamed(context, AppRoutes.page, arguments: 'terms'), showDivider: false),
              ],
            ),
          ),
          const SizedBox(height: 14),
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: ColorConstant.surfaceCl,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: ColorConstant.lightBorderCl),
            ),
            child: Column(
              children: [
                InkWell(
                  onTap: () => _confirmDelete(context),
                  child: const Row(
                    children: [
                      MenuIconBox(icon: Icons.delete_outline, background: ColorConstant.deleteRedBg, color: ColorConstant.redLSecCl),
                      SizedBox(width: 14),
                      Text(
                        'Delete Account',
                        style: TextStyle(
                          color: ColorConstant.redLSecCl,
                          fontWeight: FontWeight.w400,
                          fontFamily: FontsStyle.regular,
                          fontSize: 16,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                const Divider(height: 1, thickness: 1, color: ColorConstant.lightBorderCl),
                const SizedBox(height: 12),
                InkWell(
                  onTap: _logoutSheet,
                  child: const Row(
                    children: [
                      MenuIconBox(icon: Icons.logout, background: ColorConstant.primaryLightCl, color: ColorConstant.primaryCl),
                      SizedBox(width: 14),
                      Text(
                        'Logout',
                        style: TextStyle(
                          color: ColorConstant.primaryCl,
                          fontWeight: FontWeight.w400,
                          fontFamily: FontsStyle.regular,
                          fontSize: 16,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _item(IconData icon, String title, VoidCallback onTap, {bool showDivider = true}) {
    return Column(
      children: [
        InkWell(
          onTap: onTap,
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 12),
            child: Row(
              children: [
                MenuIconBox(icon: icon),
                const SizedBox(width: 14),
                Expanded(
                  child: Text(
                    title,
                    style: const TextStyle(
                      color: ColorConstant.darkGrayCl,
                      fontWeight: FontWeight.w500,
                      fontFamily: FontsStyle.medium,
                      fontSize: 15,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
        if (showDivider) const Divider(height: 1, thickness: 1, color: ColorConstant.lightGrayCl1),
      ],
    );
  }

  Future<void> _logoutSheet() async {
    await showModalBottomSheet<void>(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) {
        return Container(
          width: double.infinity,
          decoration: const BoxDecoration(
            color: ColorConstant.surfaceCl,
            borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          ),
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
          child: SafeArea(
            top: false,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text('Logout', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, fontFamily: FontsStyle.bold, color: ColorConstant.darkGrayCl)),
                const SizedBox(height: 12),
                const Divider(),
                const SizedBox(height: 16),
                const Text('Are you sure you want to logout?', style: TextStyle(color: ColorConstant.lightGrayIcCl)),
                const SizedBox(height: 20),
                Row(
                  children: [
                    Expanded(
                      child: CustomButton(
                        style: CustomButtonStyle.outline,
                        text: 'Cancel',
                        onPressed: () => Navigator.pop(sheetContext),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomButton(
                        text: 'Logout',
                        onPressed: () {
                          Navigator.pop(sheetContext);
                          _logout();
                        },
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _logout() async {
    await context.read<AuthProvider>().logout();
    if (!mounted) return;
    Navigator.pushNamedAndRemoveUntil(context, AppRoutes.mobile, (route) => false);
  }

  Future<void> _confirmDelete(BuildContext context) async {
    final confirmed = await showModalBottomSheet<bool>(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) {
        return Container(
          width: double.infinity,
          decoration: const BoxDecoration(
            color: ColorConstant.surfaceCl,
            borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          ),
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
          child: SafeArea(
            top: false,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text(
                  'Delete Account',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, fontFamily: FontsStyle.bold, color: ColorConstant.darkGrayCl),
                ),
                const SizedBox(height: 12),
                const Divider(color: ColorConstant.lightGrayCl1),
                const SizedBox(height: 16),
                const Text(
                  'This is permanent. Your name and mobile number will be removed, and your subscription will end.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 14, fontFamily: FontsStyle.regular, color: ColorConstant.darkGrayCl),
                ),
                const SizedBox(height: 20),
                Row(
                  children: [
                    Expanded(
                      child: CustomButton(
                        style: CustomButtonStyle.outline,
                        text: 'Cancel',
                        onPressed: () => Navigator.pop(sheetContext, false),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: CustomButton(
                        text: 'Delete',
                        onPressed: () => Navigator.pop(sheetContext, true),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
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
}
