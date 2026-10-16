import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/form_chrome.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/help/providers/content_provider.dart';
import 'package:provider/provider.dart';

class CmsPageView extends StatefulWidget {
  const CmsPageView({super.key, required this.slug});

  final String slug;

  @override
  State<CmsPageView> createState() => _CmsPageViewState();
}

class _CmsPageViewState extends State<CmsPageView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ContentProvider>().loadPage(widget.slug);
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<ContentProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: GreenAppBar(title: state.pageTitle.isEmpty ? 'MyBaap' : state.pageTitle),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null
              ? StateMessage(title: 'Page not available', subtitle: state.error, onRetry: () => state.loadPage(widget.slug))
              : state.pageContent.isEmpty
                  ? const StateMessage(title: 'Page not available')
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: WhiteCard(
                        child: Html(
                        data: state.pageContent,
                        style: {
                          'body': Style(
                            margin: Margins.zero,
                            fontFamily: FontsStyle.regular,
                            color: ColorConstant.darkGrayCl,
                            fontSize: FontSize(14),
                            lineHeight: const LineHeight(1.45),
                          ),
                        },
                      ),
                      ),
                    ),
    );
  }
}
