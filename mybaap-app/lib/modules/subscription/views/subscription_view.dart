import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/subscription/models/plan_model.dart';
import 'package:mybaap/modules/subscription/providers/subscription_provider.dart';
import 'package:provider/provider.dart';

class SubscriptionView extends StatefulWidget {
  const SubscriptionView({super.key});

  @override
  State<SubscriptionView> createState() => _SubscriptionViewState();
}

class _SubscriptionViewState extends State<SubscriptionView> {
  int? _selectedId;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => context.read<SubscriptionProvider>().load());
  }

  void _pay(PlanModel plan) {
    showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Payment hook'),
        content: Text(
          '${plan.name} (₹${plan.price.toStringAsFixed(0)}) is ready for a payment gateway. '
          'The server activates a plan when POST /payments/webhook is called. This button is a placeholder.',
        ),
        actions: [TextButton(onPressed: () => Navigator.pop(context), child: const Text('OK'))],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<SubscriptionProvider>();
    return Scaffold(
      appBar: AppBar(title: const Text('Subscription', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null
              ? StateMessage(title: 'Could not load plans', subtitle: state.error, onRetry: state.load)
              : state.plans.isEmpty
                  ? StateMessage(title: 'No plans available', onRetry: state.load)
                  : ListView(
                      padding: const EdgeInsets.all(16),
                      children: [
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: ColorConstant.primaryLightCl,
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Text(
                            state.isActive
                                ? 'Active${state.planName == null ? '' : ' · ${state.planName}'}${state.endDate == null ? '' : ' until ${state.endDate}'}'
                                : 'You do not have an active subscription.',
                            style: const TextStyle(fontFamily: FontsStyle.medium),
                          ),
                        ),
                        const SizedBox(height: 16),
                        ...state.plans.map((plan) {
                          final selected = (_selectedId ?? state.plans.first.id) == plan.id;
                          return Card(
                            color: ColorConstant.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                              side: BorderSide(color: selected ? ColorConstant.primaryCl : ColorConstant.cardBorderCl, width: selected ? 2 : 1),
                            ),
                            child: ListTile(
                              onTap: () => setState(() => _selectedId = plan.id),
                              title: Text(plan.name, style: const TextStyle(fontFamily: FontsStyle.semiBold)),
                              subtitle: Text(plan.description ?? '${plan.durationDays} days'),
                              trailing: Text('₹${plan.price.toStringAsFixed(0)}', style: const TextStyle(fontFamily: FontsStyle.bold, color: ColorConstant.primaryCl)),
                            ),
                          );
                        }),
                        const SizedBox(height: 16),
                        CustomButton(
                          text: 'Pay',
                          onPressed: () {
                            final id = _selectedId ?? state.plans.first.id;
                            _pay(state.plans.firstWhere((plan) => plan.id == id));
                          },
                        ),
                      ],
                    ),
    );
  }
}
