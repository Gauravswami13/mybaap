import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/custom_button.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/subscription/providers/purchase_history_provider.dart';
import 'package:mybaap/routes/app_routes.dart';
import 'package:provider/provider.dart';

class PurchaseHistoryView extends StatefulWidget {
  const PurchaseHistoryView({super.key});

  @override
  State<PurchaseHistoryView> createState() => _PurchaseHistoryViewState();
}

class _PurchaseHistoryViewState extends State<PurchaseHistoryView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PurchaseHistoryProvider>().load();
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<PurchaseHistoryProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: AppBar(title: const Text('Purchase History', style: TextStyle(fontFamily: FontsStyle.semiBold))),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null && state.items.isEmpty
              ? StateMessage(title: 'Could not load purchases', subtitle: state.error, onRetry: state.load)
              : state.items.isEmpty
                  ? Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Text(
                              'Abhi tak koi subscription nahi',
                              textAlign: TextAlign.center,
                              style: TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 16),
                            ),
                            const SizedBox(height: 16),
                            CustomButton(
                              text: 'View plans',
                              onPressed: () => Navigator.pushNamed(context, AppRoutes.subscription),
                            ),
                          ],
                        ),
                      ),
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: state.items.length + (state.hasMore ? 1 : 0),
                      itemBuilder: (context, index) {
                        if (index >= state.items.length) {
                          return Padding(
                            padding: const EdgeInsets.only(top: 8),
                            child: CustomButton(
                              text: state.loadingMore ? 'Loading...' : 'Load more',
                              enabled: !state.loadingMore,
                              onPressed: state.loadMore,
                            ),
                          );
                        }
                        return _card(state.items[index]);
                      },
                    ),
    );
  }

  Widget _card(PurchaseItem item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: ColorConstant.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: ColorConstant.cardBorderCl),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(child: Text(item.planName, style: const TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 16))),
              _badge(item.status),
            ],
          ),
          const SizedBox(height: 8),
          Text('Rs ${item.price}', style: const TextStyle(fontFamily: FontsStyle.medium, color: ColorConstant.primaryCl)),
          const SizedBox(height: 4),
          Text('${item.startDate} – ${item.endDate}', style: const TextStyle(color: ColorConstant.bodyTextCl, fontFamily: FontsStyle.regular)),
        ],
      ),
    );
  }

  Widget _badge(String status) {
    final color = switch (status) {
      'active' => ColorConstant.primaryCl,
      'cancelled' => ColorConstant.errorCl,
      _ => ColorConstant.hintTextCl,
    };
    final label = status.isEmpty ? 'Unknown' : '${status[0].toUpperCase()}${status.substring(1)}';
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(8)),
      child: Text(label, style: TextStyle(color: color, fontFamily: FontsStyle.medium, fontSize: 12)),
    );
  }
}
