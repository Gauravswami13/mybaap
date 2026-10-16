import 'package:flutter/material.dart';
import 'package:mybaap/modules/auth/providers/auth_provider.dart';
import 'package:mybaap/modules/home/providers/home_provider.dart';
import 'package:mybaap/services/api_client.dart';
import 'package:mybaap/services/api_logs.dart';
import 'package:razorpay_flutter/razorpay_flutter.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:mybaap/modules/subscription/models/plan_model.dart';
import 'package:mybaap/modules/subscription/providers/subscription_provider.dart';
import 'package:provider/provider.dart';

class SubscriptionView extends StatefulWidget {
  const SubscriptionView({super.key, this.embedded = false});

  final bool embedded;

  @override
  State<SubscriptionView> createState() => _SubscriptionViewState();
}

class _SubscriptionViewState extends State<SubscriptionView> {
  late final Razorpay _razorpay;
  PlanModel? _payingPlan;

  @override
  void initState() {
    super.initState();
    _razorpay = Razorpay();
    _razorpay.on(Razorpay.EVENT_PAYMENT_SUCCESS, _onSuccess);
    _razorpay.on(Razorpay.EVENT_PAYMENT_ERROR, _onError);
    _razorpay.on(Razorpay.EVENT_EXTERNAL_WALLET, _onWallet);
    WidgetsBinding.instance.addPostFrameCallback((_) => context.read<SubscriptionProvider>().load());
  }

  @override
  void dispose() {
    _razorpay.clear();
    super.dispose();
  }

  Future<void> _pay(PlanModel plan) async {
    final plans = context.read<SubscriptionProvider>();
    try {
      final order = await plans.createOrder(plan.id);
      if (!mounted) return;
      Log.console(order);
      final key = order['key_id']?.toString() ?? '';
      final orderId = order['order_id']?.toString() ?? '';
      final amount = order['amount'] is num ? (order['amount'] as num).round() : int.tryParse('${order['amount']}') ?? 0;
      if (key.isEmpty || orderId.isEmpty || amount < 100) {
        _showMessage('Razorpay order was not created. Add RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET on the server, then run php artisan config:clear.');
        return;
      }
      _payingPlan = plan;
      final mobile = context.read<AuthProvider>().user?.mobile ?? '';
      _razorpay.open({
        'key': key,
        'amount': amount,
        'currency': 'INR',
        'name': 'MyBaap',
        'description': plan.name,
        'order_id': orderId,
        'prefill': {'contact': mobile, 'email': 'test@mybaap.local'},
      });
    } on ApiException catch (error) {
      if (!mounted) return;
      _showMessage(error.message);
    } catch (error) {
      if (!mounted) return;
      _showMessage(error.toString());
    }
  }

  void _showMessage(String message) {
    showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Payment'),
        content: Text(message),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('OK')),
        ],
      ),
    );
  }

  Future<void> _onSuccess(PaymentSuccessResponse response) async {
    final plan = _payingPlan;
    final orderId = response.orderId;
    final paymentId = response.paymentId;
    final signature = response.signature;
    if (plan == null || orderId == null || paymentId == null || signature == null) return;
    try {
      await context.read<SubscriptionProvider>().verifyPayment(
        planId: plan.id,
        orderId: orderId,
        paymentId: paymentId,
        signature: signature,
      );
      if (!mounted) return;
      await context.read<AuthProvider>().loadProfile();
      if (!mounted) return;
      await context.read<HomeProvider>().load();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Subscription activated')));
    } on ApiException catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(error.message)));
    }
  }

  void _onError(PaymentFailureResponse response) {
    if (!mounted) return;
    _showMessage(response.message ?? 'Payment was not completed.');
  }

  void _onWallet(ExternalWalletResponse response) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response.walletName ?? 'Wallet payment was not completed.')));
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<SubscriptionProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: widget.embedded
          ? null
          : GreenAppBar(
        title: 'Subscription',
        actions: [
          GestureDetector(
            onTap: () => Navigator.pushNamed(context, AppRoutes.purchases),
            child: const Padding(
              padding: EdgeInsets.only(right: 12),
              child: Text(
                'History',
                style: TextStyle(color: ColorConstant.white, fontSize: 12, fontFamily: FontsStyle.regular),
              ),
            ),
          ),
        ],
      ),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null
              ? StateMessage(title: 'Could not load plans', subtitle: state.error, onRetry: state.load)
              : state.plans.isEmpty
                  ? StateMessage(title: 'No plans available', onRetry: state.load)
                  : ListView(
                      padding: EdgeInsets.fromLTRB(16, widget.embedded ? 36 : 16, 16, 16),
                      children: [
                        if (widget.embedded) ...[
                          const Align(
                            alignment: Alignment.centerLeft,
                            child: Text('Plans', style: TextStyle(color: ColorConstant.darkGrayCl, fontFamily: FontsStyle.bold, fontSize: 22)),
                          ),
                          const SizedBox(height: 12),
                        ],
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: const Color(0xFF3A2A08),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: const Color(0xFFE6A817)),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.workspace_premium_outlined, color: Color(0xFFE6A817), size: 28),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      state.isActive ? (state.planName ?? 'Active plan') : 'No active plan',
                                      style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 18),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      state.isActive
                                          ? 'Valid${state.endDate == null ? '' : ' till ${state.endDate}'}'
                                          : 'You do not have an active subscription.',
                                      style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 18),
                        ...state.plans.map((plan) {
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 14),
                            child: Container(
                              width: double.infinity,
                              padding: const EdgeInsets.fromLTRB(16, 20, 16, 16),
                              decoration: BoxDecoration(
                                color: ColorConstant.surfaceCl,
                                borderRadius: BorderRadius.circular(18),
                                border: Border.all(color: ColorConstant.lightBorderCl),
                              ),
                              child: Column(
                                children: [
                                  Text(plan.name, textAlign: TextAlign.center, style: const TextStyle(color: ColorConstant.white, fontSize: 22, fontFamily: FontsStyle.bold)),
                                  if (plan.description != null && plan.description!.isNotEmpty) ...[
                                    const SizedBox(height: 8),
                                    Text(plan.description!, textAlign: TextAlign.center, style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13, height: 1.35)),
                                  ],
                                  const SizedBox(height: 14),
                                  Text(
                                    '₹${plan.price.toStringAsFixed(0)}  /  ${plan.durationDays} days',
                                    style: const TextStyle(color: ColorConstant.white, fontFamily: FontsStyle.bold, fontSize: 18),
                                  ),
                                  const SizedBox(height: 12),
                                  const _Perk('All paid videos unlock'),
                                  const _Perk('Playback on this Android app'),
                                  _Perk('${plan.durationDays} days of access'),
                                  const SizedBox(height: 14),
                                  CustomButton(
                                    text: state.paying && _payingPlan?.id == plan.id ? 'Please wait...' : 'Choose plan',
                                    enabled: !state.paying,
                                    onPressed: () => _pay(plan),
                                  ),
                                ],
                              ),
                            ),
                          );
                        }),
                      ],
                    ),
    );
  }
}

class _Perk extends StatelessWidget {
  const _Perk(this.label);

  final String label;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        children: [
          const Icon(Icons.check_rounded, color: ColorConstant.primaryCl, size: 18),
          const SizedBox(width: 8),
          Expanded(child: Text(label, style: const TextStyle(color: ColorConstant.lightGrayIcCl, fontSize: 13))),
        ],
      ),
    );
  }
}
