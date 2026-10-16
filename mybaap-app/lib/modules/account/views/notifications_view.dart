import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/account/providers/notification_provider.dart';
import 'package:provider/provider.dart';

class NotificationsView extends StatefulWidget {
  const NotificationsView({super.key});

  @override
  State<NotificationsView> createState() => _NotificationsViewState();
}

class _NotificationsViewState extends State<NotificationsView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<NotificationProvider>().load();
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<NotificationProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: const GreenAppBar(title: 'Notification'),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null && state.items.isEmpty
              ? StateMessage(title: 'Could not load notifications', subtitle: state.error, onRetry: state.load)
              : state.items.isEmpty
                  ? const StateMessage(title: 'No notifications yet')
                  : ListView.separated(
                      padding: const EdgeInsets.all(16),
                      itemCount: state.items.length,
                      separatorBuilder: (_, _) => const SizedBox(height: 10),
                      itemBuilder: (context, index) {
                        final item = state.items[index];
                        return Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: ColorConstant.surfaceCl,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: ColorConstant.lightBorderCl),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(item.title, style: const TextStyle(fontFamily: FontsStyle.semiBold, fontSize: 16, color: ColorConstant.darkGrayCl)),
                              const SizedBox(height: 6),
                              Text(item.body, style: const TextStyle(color: ColorConstant.bodyTextCl, fontFamily: FontsStyle.regular)),
                            ],
                          ),
                        );
                      },
                    ),
    );
  }
}
